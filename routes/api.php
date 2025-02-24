<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SellerApplicationController;

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/seller-applications', [SellerApplicationController::class, 'store']);
