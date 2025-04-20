<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;



Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::post('/change-password', [ApiController::class, 'changePassword']);

});

Route::post('/forgot-password', [ApiController::class, 'forgotPassword']);
Route::post('/reset-password', [ApiController::class, 'resetPassword']);
