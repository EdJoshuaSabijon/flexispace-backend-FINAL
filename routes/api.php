<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\NotificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Public Routes - No auth required
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/email/resend-verification', [AuthController::class, 'resendVerification']);

/*
|--------------------------------------------------------------------------
| Public Product Routes
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (no email verification required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
});

/*
|--------------------------------------------------------------------------
| Customer Routes (must be email verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Orders
    Route::get('/orders',    [OrderController::class, 'index']);
    Route::post('/orders',   [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Profile
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All marked as read.']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Orders
    Route::get('/orders',            [AdminOrderController::class, 'index']);
    Route::get('/orders/export',     [AdminOrderController::class, 'export']);
    Route::get('/orders/{order}',    [AdminOrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);

    // Customers
    Route::get('/customers',        [AdminCustomerController::class, 'index']);
    Route::get('/customers/{user}', [AdminCustomerController::class, 'show']);
    Route::delete('/customers/{user}', [AdminCustomerController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Admin Product Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/products',            [ProductController::class, 'store']);
    Route::put('/products/{id}',        [ProductController::class, 'update']);
    Route::delete('/products/{id}',     [ProductController::class, 'destroy']);
    Route::post('/products/{id}/image', [ProductController::class, 'uploadImage']);
});
