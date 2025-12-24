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
class ApartmentController extends Controller
{
    public function getAllApartments()
    {
        $apartments = Apartment::with('user:id,first_name,last_name,phone')->get();
        $data = $apartments->map(function ($apartment) {
            return [
                'id'             => $apartment->id,
                'site'           => $apartment->site,
                'type'           => $apartment->type,
                'area'           => $apartment->area,
                'number_of_room' => $apartment->number_of_room,
                'city'           => $apartment->city,
                'price'          => $apartment->price,
                'rating'         => $apartment->rating,
                'description'    => $apartment->description,
                'image'          => $apartment->image,
                'owner' => [
                    'name'  => $apartment->user->first_name . ' ' . $apartment->user->last_name,
                    'phone' => $apartment->user->phone
                ]
            ];
        });
     return response()->json($data,200);
    }
    public function postApartment(StoreApartmentRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $path1=null;
        if ($request->hasFile('image')) {
            $path1 = $request->file('image')->store('imageApartment', 'public');
            $data['image'] = $path1;
        }
        $apartment = Apartment::create($validated);
        return response()->json([
            'owner' => [
                'name' => $user->first_name . ' ' . $user->last_name,
                'phone' => $user->phone
            ],
            'apartment' => $apartment
        ], 201);
    }

    public function addToFavorites($apartmentId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }
        Apartment::findOrFail($apartmentId);
        $user->favoriteApartments()->syncWithoutDetaching([$apartmentId]);

        return response()->json([
            'success' => true,
            'message' => 'Apartment added to favorites'
        ], 200);
    }
    public function removeFromFavorites($apartmentId)
    {
        Apartment::findOrFail($apartmentId);
        Auth::user()->favoriteApartments()->detach($apartmentId);
        return response()->json(['message' => 'Apartment removed from favorites'], 200);
    }
    public function getFavoriteApartments()
    {
        $apartments = Auth::user()->favoriteApartments()->get();
        return response()->json($apartments, 200);
    }


    public function filterApartments(Request $request)
    {
        $query = Apartment::with('user:id,first_name,last_name,phone');
        
        
        if ($request->filled('governorate')) {
            $query->where('governorate', 'like', '%' . $request->governorate . '%');
        }
        
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        
        if ($request->filled('type') && in_array($request->type, ['home', 'villa', 'warehouse'])) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', (int)$request->min_price);
        }
        
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', (int)$request->max_price);
        }
        
        if ($request->filled('number_of_room') && is_numeric($request->number_of_room)) {
            $query->where('number_of_room', '>=', (int)$request->number_of_room);
        }
        
        if ($request->filled('min_area') && is_numeric($request->min_area)) {
            $query->where('area', '>=', (int)$request->min_area);
        }
        
        if ($request->filled('max_area') && is_numeric($request->max_area)) {
            $query->where('area', '<=', (int)$request->max_area);
        }
        
        if ($request->filled('min_rating') && is_numeric($request->min_rating)) {
            $query->where('rating', '>=', (int)$request->min_rating);
        }
        
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('site', 'like', '%' . $searchTerm . '%')
                  ->orWhere('city', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            default: $query->orderBy('created_at', 'desc');
        }
        
        $limit = $request->get('limit', 50);
        $query->limit(min($limit, 100));
        
        $apartments = $query->get();
        
        $data = $apartments->map(function ($apartment) {
            return [
                'id' => $apartment->id,
                'site' => $apartment->site,
                'type' => $apartment->type,
                'area' => $apartment->area,
                'number_of_room' => $apartment->number_of_room,
                'city' => $apartment->city,
                'governorate' => $apartment->governorate,
                'price' => $apartment->price,
                'rating' => $apartment->rating,
                'description' => $apartment->description,
                'image' => $apartment->image,
                'owner' => [
                    'name' => $apartment->user->first_name . ' ' . $apartment->user->last_name,
                    'phone' => $apartment->user->phone
                ]
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $data->count()
        ], 200);
    }
}
