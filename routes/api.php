<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LogisticsProviderController;
use App\Http\Controllers\GcashSettingsController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\Admin\FinancialController;
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
Route::post('/test-email', [TestEmailController::class, 'sendTestEmail']);

/*
|--------------------------------------------------------------------------
| Public Product Routes
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Public Logistics Routes
|--------------------------------------------------------------------------
*/
Route::get('/logistics-providers', [LogisticsProviderController::class, 'index']);

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
    Route::patch('/orders/{id}/cancel', [OrderController::class, 'cancel']);

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

    // Returns
    Route::get('/returns', [ReturnController::class, 'index']);
    Route::post('/returns', [ReturnController::class, 'store']);
    Route::get('/returns/{id}', [ReturnController::class, 'show']);

    // GCash Settings (public for customers to see)
    Route::get('/gcash-settings', [GcashSettingsController::class, 'index']);
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
    Route::patch('/orders/{order}/reject', [AdminOrderController::class, 'rejectOrder']);

    // Customers
    Route::get('/customers',        [AdminCustomerController::class, 'index']);
    Route::get('/customers/{user}', [AdminCustomerController::class, 'show']);
    Route::delete('/customers/{user}', [AdminCustomerController::class, 'destroy']);

    // Logistics Providers
    Route::get('/logistics-providers', [LogisticsProviderController::class, 'adminIndex']);
    Route::post('/logistics-providers', [LogisticsProviderController::class, 'store']);
    Route::put('/logistics-providers/{id}', [LogisticsProviderController::class, 'update']);
    Route::delete('/logistics-providers/{id}', [LogisticsProviderController::class, 'destroy']);

    // GCash Settings
    Route::put('/gcash-settings', [GcashSettingsController::class, 'update']);

    // Returns Management
    Route::get('/returns', [ReturnController::class, 'adminIndex']);
    Route::patch('/returns/{id}/status', [ReturnController::class, 'updateStatus']);

    // Financial
    Route::get('/financial/report', [FinancialController::class, 'getReport']);
    Route::get('/financial/export', [FinancialController::class, 'getExportData']);
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
