<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/register', function () {
//     return view('auth.register');
// })->name('register');

// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');

// Route::get('/u/dashboard', function () {
//     return view('dashboards.user');
// })->middleware('auth:sanctum')->name('user.dashboard');

// Route::get('/a/dashboard', function () {
//     return view('dashboards.admin');
// })->middleware('auth:sanctum')->name('admin.dashboard');