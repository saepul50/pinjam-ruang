<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\RoomCategoryController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::get('/room-categories', [RoomCategoryController::class, 'index']);
Route::get('/room-categories/{id}', [RoomCategoryController::class, 'show']);
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::get('/rooms/{id}/availability', [RoomController::class, 'availability']);

Route::middleware(['api.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['admin'])->group(function () {
        Route::post('/room-categories', [RoomCategoryController::class, 'store']);
        Route::put('/room-categories/{id}', [RoomCategoryController::class, 'update']);
        Route::delete('/room-categories/{id}', [RoomCategoryController::class, 'destroy']);
    });

    Route::middleware(['staff'])->group(function () {
        Route::post('/rooms', [RoomController::class, 'store']);
        Route::put('/rooms/{id}', [RoomController::class, 'update']);
        Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);
    });

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);

    Route::middleware(['staff'])->group(function () {
        Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
    });
});
