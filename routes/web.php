<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomCategoryController;
use App\Http\Controllers\RoomController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('rooms.show');

    Route::middleware(['staff'])->group(function () {
        Route::resource('rooms', RoomController::class)->except(['index', 'show']);

        Route::patch('/bookings/{id}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
        Route::patch('/bookings/{id}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('/bookings/{id}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    });

    Route::middleware(['admin'])->group(function () {
        Route::resource('room-categories', RoomCategoryController::class)->except(['show']);
    });
});
