<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategiriesController;
use App\Http\Controllers\api\ProductController;
use Illuminate\Support\Facades\Route;


//public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategiriesController::class, 'index']);
Route::get('/banner', [CategiriesController::class, 'bannerImages']);
Route::get('forget-password', [AuthController::class, 'sendCode'])->middleware('throttle:2,1');
Route::post('forget-password', [AuthController::class, 'updatePassword'])->middleware('throttle:2,1');
Route::get('consultant', [AuthController::class, 'consultant']);

//auth needed Route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:2,1');
    Route::post('verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::get('/user', [AuthController::class, 'user']);
    Route::resource('product', ProductController::class);
});
