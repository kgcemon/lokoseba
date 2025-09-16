<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategiriesController;
use Illuminate\Support\Facades\Route;


//public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategiriesController::class, 'index']);
Route::get('/banner', [CategiriesController::class, 'banner_images']);

//auth needed Route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

});
