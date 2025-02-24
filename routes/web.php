<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\ReviewController;

// Admin API routes
Route::group(['prefix' => 'admin'], function () {
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/customers', [AdminController::class, 'getCustomers']);
        Route::get('/employees', [AdminController::class, 'getEmployees']);
        Route::get('/products', [AdminController::class, 'getProducts']);
        Route::get('/orders', [AdminController::class, 'getOrders']);
        Route::get('/order-details', [AdminController::class, 'getOrderDetails']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/employees', [AdminController::class, 'storeEmployee']);
        Route::delete('/employees/{id}', [AdminController::class, 'deleteEmployee']);
        Route::delete('/customers/{id}', [AdminController::class, 'deleteCustomer']);
        Route::post('/assign-employee', [AdminController::class, 'assignEmployee']);
        Route::delete('/products/{id}', [AdminController::class, 'deleteProduct']);
        Route::get('/seller-applications', [AdminController::class, 'getSellerApplications']);
        Route::post('/seller-applications/{id}/approve', [AdminController::class, 'approveSellerApplication']);
        Route::post('/seller-applications/{id}/reject', [AdminController::class, 'rejectSellerApplication']);
    });
});

// Regular auth routes
Route::post('/register', [RegisterController::class, 'register'])->name('register.customer');
Route::post('/register/seller', [SellerApplicationController::class, 'store'])->name('register.seller');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

// Profile Routes - protected by auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/upload-picture', [ProfileController::class, 'uploadProfilePicture']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/my-products', [ProductController::class, 'myProducts']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

// API routes for profile data
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/profile', [ProfileController::class, 'show']);
    Route::put('/api/profile', [ProfileController::class, 'update']);
});

// API routes
Route::post('/api/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/api/orders/history', [OrderController::class, 'history'])->middleware('auth');
Route::get('/api/orders/{orderId}', [OrderController::class, 'show']);
Route::delete('/api/orders/{orderId}/cancel', [OrderController::class, 'cancel']);
Route::post('/api/reviews', [ReviewController::class, 'store']);
Route::get('/api/reviews', [ReviewController::class, 'index']);

// Catch all routes for Vue.js SPA - Keep these at the bottom
Route::get('/admin/{path?}', function () {
    return view('app');
})->where('path', '.*');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|admin).*');
