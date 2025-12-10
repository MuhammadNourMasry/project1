<?php

use App\Http\Controllers\ApartmentController;
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
    Route::post('logout', [UserController::class, 'logout']);

    //! مسارات مشتركة للجميع
    Route::get('getApartments', [ApartmentController::class, 'getAllApartments']);
    //! مسارات للمستأجرين فقط (يمكن للمدير أيضاً)
    Route::middleware('role:tenant,admin')->group(function () {
        Route::post('book', [ApartmentController::class, 'storeBooking']);
        Route::get('my-bookings', [ApartmentController::class, 'getMyBookings']);//
        Route::post('bookings/{bookingId}/cancel', [ApartmentController::class, 'cancelBooking']);
        Route::put('bookings/{bookingId}', [ApartmentController::class, 'updateBooking']);
        Route::post('check-availability', [ApartmentController::class, 'checkAvailability']);
        Route::post('storeRating',[ApartmentController::class,'storeRating']);
    });
    //! مسارات لأصحاب الشقق فقط (يمكن للمدير أيضاً)
    Route::middleware('role:rented,admin')->group(function () {
        Route::post('postApartment', [ApartmentController::class, 'postApartment']);
        Route::get('ownerBookings', [ApartmentController::class, 'getOwnerBookings']);
        Route::post('bookings/{bookingId}/approve', [ApartmentController::class, 'approveBooking']);
    });

    
    /*
    للاستخدام لاحقا
    //! مسارات للمدير فقط
    Route::middleware('role:admin')->group(function () {
        Route::get('getaAllUser', [UserController::class, 'getaAllUser']);
        Route::delete('removeUser', [UserController::class, 'deleteUser']);
    });*/
});