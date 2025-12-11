<?php


namespace App\Http\Controllers;
use App\Http\Requests\CheckBookingRequest;
use App\Http\Requests\RatingRequest;
use App\Http\Requests\StoreApartmentRequest;
use App\Models\Apartment;
use App\Models\Booking;
use App\Models\Rating;
use App\Services\BookingService;
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Event\ResponseEvent;


class BookingController extends Controller
{
    private function isApartmentOwner($apartmentId)
{
    $user = Auth::user();
    $apartment = \App\Models\Apartment::find($apartmentId);
    
    if (!$apartment) {
        return false;
    }
    
    return $apartment->user_id === $user->id;
}
      public function storeBooking(CheckBookingRequest $request)
    {
        try {
            $bookingService = new BookingService();
            $booking = DB::transaction(function () use ($request, $bookingService) {
                return $bookingService->createBooking(
                    Auth::id(),
                    $request->apartment_id,
                    $request->check_in,
                    $request->check_out
                );
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Booking request created successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'apartment_id' => $booking->apartment_id,
                    'check_in' => $booking->check_in,
                    'check_out' => $booking->check_out,
                    'status' => $booking->status,
                    'total_price' => $this->calculateTotalPrice($booking->apartment_id, $booking->check_in, $booking->check_out)
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'BOOKING_CONFLICT'
            ], 409);
        }
    }
    
    public function getMyBookings()
    {
        Booking::where('user_id', Auth::id())
    ->where('status', 'approved')
    ->whereDate('check_out', '<', now()->toDateString())
    ->update(['status' => 'end']);
        $user = Auth::user();
        $bookings = Booking::with(['apartment', 'apartment.user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'apartment' => [
                        'id' => $booking->apartment->id,
                        'site' => $booking->apartment->site,
                        'city' => $booking->apartment->city,
                        'type' => $booking->apartment->type,
                        'price_per_night' => $booking->apartment->price,
                        'owner_name' => $booking->apartment->user->first_name . ' ' . $booking->apartment->user->last_name
                    ],
                    'check_in' => $booking->check_in->format('Y-m-d'),
                    'check_out' => $booking->check_out->format('Y-m-d'),
                    'status' => $booking->status,
                    'total_nights' => $booking->check_in->diffInDays($booking->check_out),
                    'total_price' => $this->calculateTotalPrice($booking->apartment_id, $booking->check_in, $booking->check_out),
                    'created_at' => $booking->created_at
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }
    
    public function getOwnerBookings()
    {
        $user = Auth::user();
        
        
        $apartmentIds = Apartment::where('user_id', $user->id)->pluck('id');
        
       
        $bookings = Booking::with(['apartment', 'user'])
            ->whereIn('apartment_id', $apartmentIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'apartment' => [
                        'id' => $booking->apartment->id,
                        'site' => $booking->apartment->site,
                        'city' => $booking->apartment->city
                    ],
                    'tenant' => [
                        'id' => $booking->user->id,
                        'name' => $booking->user->first_name . ' ' . $booking->user->last_name,
                        'phone' => $booking->user->phone
                    ],
                    'check_in' => $booking->check_in->format('Y-m-d'),
                    'check_out' => $booking->check_out->format('Y-m-d'),
                    'status' => $booking->status,
                    'total_nights' => $booking->check_in->diffInDays($booking->check_out),
                    'total_price' => $this->calculateTotalPrice($booking->apartment_id, $booking->check_in, $booking->check_out),
                    'created_at' => $booking->created_at
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }
    
    public function approveBooking(Request $request, $bookingId)
{
    $request->validate([
        'action' => 'required|in:approve,reject'
    ]);
    
    try {
        $bookingService = new BookingService();
        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        
       
        $booking = \App\Models\Booking::with('apartment')->find($bookingId);
        
        if ($booking && !$this->isApartmentOwner($booking->apartment_id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not the owner of this apartment'
            ], 403);
        }
        
        $booking = $bookingService->updateBookingStatus(
            $bookingId,
            $status,
            Auth::id()
        );
        
        return response()->json([
            'success' => true,
            'message' => "Booking {$status} successfully",
            'data' => $booking
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}
    public function cancelBooking($bookingId)
    {
        try {
            $user = Auth::user();
            $booking = Booking::findOrFail($bookingId);
            
            
            if ($booking->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this booking'
                ], 403);
            }
            
            if (now()->gte($booking->check_in)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel a booking that has already started'
                ], 400);
            }
            
            $booking->status = 'cancelled';
            $booking->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'data' => $booking
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function updateBooking(Request $request, $bookingId)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in'
        ]);
        
        try {
            $user = Auth::user();
            $booking = Booking::findOrFail($bookingId);
            
            if ($booking->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this booking'
                ], 403);
            }
            
            if (!in_array($booking->status, ['pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update a cancelled or rejected booking'
                ], 400);
            }
            
      
            $bookingService = new BookingService();
            
            if (!$bookingService::isApartmentAvailable(
                $booking->apartment_id,
                $request->check_in,
                $request->check_out,
                $user->id,
                $bookingId
            )) {
                return response()->json([
                    'success' => false,
                    'message' => 'Apartment not available for the new dates'
                ], 409);
            }
            
     
            $booking->check_in = $request->check_in;
            $booking->check_out = $request->check_out;
            
            if ($booking->status === 'approved') {
                $booking->status = 'pending';
            }
            
            $booking->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => $booking
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    private function calculateTotalPrice($apartmentId, $checkIn, $checkOut)
    {
        $apartment = Apartment::find($apartmentId);
        
        if (!$apartment) {
            return 0;
        }
        
        $checkInDate = \Carbon\Carbon::parse($checkIn);
        $checkOutDate = \Carbon\Carbon::parse($checkOut);
        $nights = $checkInDate->diffInDays($checkOutDate);
        
        return $apartment->price * $nights;
    }


public function checkAvailability(Request $request)
{
    $request->validate([
        'apartment_id' => 'required|exists:apartments,id',
        'check_in' => 'required|date|after_or_equal:today',
        'check_out' => 'required|date|after:check_in',
    ]);
    
    $bookingService = new BookingService();
    
    if ($bookingService::hasUserBookingForSamePeriod(
        Auth::id(),
        $request->apartment_id,
        $request->check_in,
        $request->check_out
    )) {
        return response()->json([
            'available' => false,
            'message' => 'You already have a booking for this apartment during this period'
        ], 409);
    }
    if (!$bookingService::isApartmentAvailable(
        $request->apartment_id,
        $request->check_in,
        $request->check_out,
        Auth::id()
    )) {
        return response()->json([
            'available' => false,
            'message' => 'The apartment is not available for the specified period'
        ], 409);
    }
    
    return response()->json([
        'available' => true,
        'message' => 'Apartment is available for booking'
    ]);
}

public function storeRating(RatingRequest $request)
{
    try {
        $user = Auth::user();
        $booking = Booking::with('apartment')->findOrFail($request->booking_id);
        if ($booking->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to rate this booking'
            ], 403);
        }
        if (now()->lt($booking->check_out)) {
            return response()->json([
                'success' => false,
                'message' => 'You can only rate after your stay has ended'
            ], 400);
        }
        if ($booking->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'You can only rate approved bookings'
            ], 400);
        }
        $existingRating = Rating::where('booking_id', $request->booking_id)->first();
        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'You have already rated this booking'
            ], 409);
        }
        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['apartment_id'] = $booking->apartment_id;
        
        $rating = Rating::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully',
            'data' => $rating
        ], 201);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to submit rating: ' . $e->getMessage()
        ], 500);
    }
}
}
