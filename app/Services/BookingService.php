<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public static function isApartmentAvailable($apartmentId, $checkIn, $checkOut, $userId = null, $excludeBookingId = null)
    {

        $checkInDate = \Carbon\Carbon::parse($checkIn);
        $checkOutDate = \Carbon\Carbon::parse($checkOut);
        $query = Booking::where('apartment_id', $apartmentId)
            ->whereIn('status', ['approved', 'pending']);

        if (!empty($userId) && $userId > 0) {
            $query->where('user_id', '<>', (int)$userId);
        }

        if (!empty($excludeBookingId) && $excludeBookingId > 0) {
            $query->where('id', '<>', (int)$excludeBookingId);
        }

        $bookings = $query->get(['check_in', 'check_out', 'user_id']);

        foreach ($bookings as $booking) {
            $bookingCheckIn = \Carbon\Carbon::parse($booking->check_in);
            $bookingCheckOut = \Carbon\Carbon::parse($booking->check_out);

            if ($checkInDate->lt($bookingCheckOut) && $checkOutDate->gt($bookingCheckIn)) {
                return false;
            }
        }

        return true;
    }

    public static function hasUserBookingForSamePeriod($userId, $apartmentId, $checkIn, $checkOut, $excludeBookingId = null)
    {
        $checkInDate = \Carbon\Carbon::parse($checkIn);
        $checkOutDate = \Carbon\Carbon::parse($checkOut);

        $query = Booking::where('user_id', $userId)
            ->where('apartment_id', $apartmentId)
            ->whereIn('status', ['approved', 'pending', 'cancelled']);

        if (!empty($excludeBookingId) && $excludeBookingId > 0) {
            $query->where('id', '<>', (int)$excludeBookingId);
        }

        $bookings = $query->get(['check_in', 'check_out']);

        foreach ($bookings as $booking) {
            $bookingCheckIn = \Carbon\Carbon::parse($booking->check_in);
            $bookingCheckOut = \Carbon\Carbon::parse($booking->check_out);

            if ($checkInDate->lt($bookingCheckOut) && $checkOutDate->gt($bookingCheckIn)) {
                return true;
            }
        }

        return false;
    }


    public function createBooking($userId, $apartmentId, $checkIn, $checkOut)
    {

        if ($checkIn >= $checkOut) {
            throw new \Exception('Check-in date must be before check-out date');
        }


        if (self::hasUserBookingForSamePeriod($userId, $apartmentId, $checkIn, $checkOut)) {
            throw new \Exception('You already have a booking for this apartment during this period');
        }


        if (!self::isApartmentAvailable($apartmentId, $checkIn, $checkOut, $userId)) {
            throw new \Exception('The apartment is not available for the specified period');
        }

        try {

            return DB::transaction(function () use ($userId, $apartmentId, $checkIn, $checkOut) {

                if (!self::isApartmentAvailable($apartmentId, $checkIn, $checkOut, $userId)) {
                    throw new \Exception('The apartment is no longer available for the specified period');
                }


                $booking = Booking::create([
                    'user_id' => $userId,
                    'apartment_id' => $apartmentId,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => 'pending'
                ]);

                return $booking;
            });

        } catch (\Exception $e) {
            throw new \Exception('Failed to create booking: ' . $e->getMessage());
        }
    }
    public function updateBookingStatus($bookingId, $status, $ownerId)
    {
        $booking = Booking::with('apartment')->find($bookingId);

        if (!$booking) {
            throw new \Exception('Booking not found');
        }


        if ($booking->apartment->user_id != $ownerId) {
            throw new \Exception('You are not authorized to modify this booking');
        }


        if ($status === 'approved') {

            if (!self::isApartmentAvailable(
                $booking->apartment_id,
                $booking->check_in,
                $booking->check_out,
                null,
                $bookingId
            )) {
                $this->cancelOverlappingPendingBookings($booking);

                if (!self::isApartmentAvailable(
                    $booking->apartment_id,
                    $booking->check_in,
                    $booking->check_out,
                    null,
                    $bookingId
                )) {
                    throw new \Exception('The apartment is no longer available for this period');
                }
            }
        }


        $booking->status = $status;
        $booking->save();

        return $booking;
    }


    private function cancelOverlappingPendingBookings(Booking $approvedBooking)
    {
        $overlappingBookings = Booking::where('apartment_id', $approvedBooking->apartment_id)
            ->where('id', '<>', $approvedBooking->id)
            ->where('status', 'pending')
            ->where(function ($query) use ($approvedBooking) {
                $query->where(function ($q) use ($approvedBooking) {
                    $q->where('check_in', '<', $approvedBooking->check_out)
                      ->where('check_out', '>', $approvedBooking->check_in);
                });
            })
            ->get();
        foreach ($overlappingBookings as $booking) {
            $booking->status = 'rejected';
            $booking->save();
        }
        return count($overlappingBookings);
    }
}
