<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Route::middleware('guest'/* , 'nocache'] */)->group(function () {
    Route::get('/', [AdminController::class, 'loginPage'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.request');
// });

Route::middleware(['auth', 'nocache'])->group(function () {
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::get('/dashboard.index', [AdminController::class, 'index'])->name('index');

    Route::get('/dashboard.registration-requests', [AdminController::class, 'getAllPendingUser'])
        ->name('registration-requests');

    Route::get('/dashboard.user-management', [AdminController::class, 'getAllUser'])
        ->name('user-management');

    Route::name('user.')->group(function () {
        Route::patch('/pending/{user}/approve', [AdminController::class, 'approve'])->name('approve');
        Route::delete('/pending/{user}/reject', [AdminController::class, 'deleteUser'])->name('reject');
        Route::delete('/user-management/{user}/delete', [AdminController::class, 'deleteUser'])->name('delete');
    });
});




//     Route::get('/', [AdminController::class, 'loginPage'])->name('login');
//     Route::post('/login', [AdminController::class, 'login'])->name('login.request');

// Route::middleware('auth')->group(function () {
//     Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

//     // Dashboard
//     Route::get('/dashboard.index', [AdminController::class, 'index'])->name('index');

//     // Users
//     Route::get('/dashboard.registration-requests', [AdminController::class, 'getAllPendingUser'])
//         ->name('registration-requests');

//     Route::get('/dashboard.user-management', [AdminController::class, 'getAllUser'])
//         ->name('user-management');

//     // Approve / Reject / Delete
//     Route::name('user.')->group(function () {
//         Route::patch('/pending/{user}/approve', [AdminController::class, 'approve'])->name('approve');
//         Route::delete('/pending/{user}/reject', [AdminController::class, 'deleteUser'])->name('reject');
//         Route::delete('/user-management/{user}/delete', [AdminController::class, 'deleteUser'])->name('delete');
//     });


// });
// Route::middleware('guest')->group(function () {
// });
// Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
