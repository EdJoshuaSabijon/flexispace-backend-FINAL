<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Login Fallback Route
| Prevents "Route [login] not defined" error
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return redirect(env('FRONTEND_URL', 'http://localhost:5174') . '/login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Email Verification Route (must be a web route, not API)
| Laravel generates signed URLs pointing to this route
|--------------------------------------------------------------------------
*/
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect(env('FRONTEND_URL', 'http://localhost:5174') . '/login?error=invalid-link');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect(env('FRONTEND_URL', 'http://localhost:5174') . '/login?verified=already');
    }

    $user->markEmailAsVerified();

    return redirect(env('FRONTEND_URL', 'http://localhost:5174') . '/login?verified=1');

})->middleware(['signed'])->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Fallback - redirect everything else to frontend
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return redirect(env('FRONTEND_URL', 'http://localhost:5174'));
});
