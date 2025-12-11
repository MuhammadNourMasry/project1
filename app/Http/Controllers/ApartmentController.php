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
      $data = $apartments->map(function($apartment) {
        return [
            'site'           => $apartment->site,
            'type'           => $apartment->type,
            'area'           => $apartment->area,
            'number_of_room' => $apartment->number_of_room,
            'city'           => $apartment->city,
            'price'          => $apartment->price,
            'description'    => $apartment->description,
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
        $apartment = Apartment::create($validated);
        /*$user_id=Auth::user()->id;
        $firstName=Auth::user()->first_name;
        $lastName=Auth::user()->last_name;
        $numberPhone=Auth::user()->phone;
        $validateData=$request->validated();
        $validateData['user_id']=$user_id;
        $validateData['owner']=$firstName.' '.$lastName;
        $validateData['owner_phone']=$numberPhone;
        $bulid=Apartment::create($validateData);
        return response()->json($bulid,201);*/
        return response()->json([
            'owner'=>[
            'name'=>$user->first_name . ' ' . $user->last_name,
            'phone'=>$user->phone
            ],
            'apartment'=>$apartment
        ],201);
    }
    
     public function addToFavorites($apartmentId)
{
    $user = Auth::user();
    
    // التحقق من تسجيل الدخول
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Please login first.'
        ], 401);
    }
    
    // التحقق من وجود الشقة
    Apartment::findOrFail($apartmentId);
    
    // إضافة للمفضلة
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
}