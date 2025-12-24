<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {

    //! مسارات مشتركة للجميع
    Route::get('getApartments', [ApartmentController::class, 'getAllApartments']);
    
    //! مسارات للمستأجرين فقط (المدير أيضاً مسموح)
    Route::middleware('role:tenant,admin')->group(function () {

        Route::post('book', [BookingController::class, 'storeBooking']);
        Route::get('my-bookings', [BookingController::class, 'getMyBookings']);
        Route::post('bookings/{bookingId}/cancel', [BookingController::class, 'cancelBooking']);
        Route::put('bookings/{bookingId}', [BookingController::class, 'updateBooking']);
        Route::post('check-availability', [BookingController::class, 'checkAvailability']);
        Route::post('storeRating', [BookingController::class, 'storeRating']);
        Route::get('filterApartments', [ApartmentController::class, 'filterApartments']);
        Route::post('apartment/{id}/favorite', [ApartmentController::class, 'addToFavorites']);
        Route::delete('apartment/{id}/favorite', [ApartmentController::class, 'removeFromFavorites']);
        Route::get('apartment/favorites', [ApartmentController::class, 'getFavoriteApartments']);
    });

    //! مسارات لأصحاب الشقق (المدير أيضاً مسموح)
    Route::middleware('role:rented,admin')->group(function () {

        Route::post('postApartment', [ApartmentController::class, 'postApartment']);
        Route::get('ownerBookings', [BookingController::class, 'getOwnerBookings']);
        Route::post('bookings/{bookingId}/approve', [BookingController::class, 'approveBooking']);
    });

});

/*
//! مسارات للمدير فقط — للاستخدام لاحقاً
Route::middleware('role:admin')->group(function () {
    Route::get('getaAllUser', [UserController::class, 'getaAllUser']);
    Route::delete('removeUser', [UserController::class, 'deleteUser']);
});
*/
