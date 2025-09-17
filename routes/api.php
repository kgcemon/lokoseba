<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategiriesController;
use Illuminate\Support\Facades\Route;


//public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategiriesController::class, 'index']);
Route::get('/banner', [CategiriesController::class, 'bannerImages']);

//auth needed Route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:2,1');
    Route::post('verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::get('/user', [AuthController::class, 'user']);
});
