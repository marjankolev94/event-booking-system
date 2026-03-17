<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Event Routes
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::post('/events', [EventController::class, 'store']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::get('/events/{id}/progress', [EventController::class, 'progress']);

    // Booking
    Route::get('/events/{id}/bookings', [BookingController::class, 'index']);
    Route::post('/events/{id}/bookings', [BookingController::class, 'store']);
    Route::patch('/bookings/{id}', [BookingController::class, 'updateStatus']);
    Route::get('/bookings/search', [BookingController::class, 'searchByEmail']);
});
