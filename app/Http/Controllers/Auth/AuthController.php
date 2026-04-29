<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'password'   => ['required', 'confirmed', Password::min(8)],
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:500',
        ]);

        // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            // If already verified, reject
            if ($existingUser->hasVerifiedEmail()) {
                return response()->json([
                    'message' => 'The email has already been taken.',
                    'errors' => ['email' => ['The email has already been taken.']]
                ], 422);
            }

            // User exists but not verified - update their info and resend email
            $existingUser->first_name = $request->first_name;
            $existingUser->last_name  = $request->last_name;
            $existingUser->name       = $request->first_name . ' ' . $request->last_name;
            $existingUser->password   = Hash::make($request->password);
            $existingUser->phone      = $request->phone ?? null;
            $existingUser->address    = $request->address ?? null;
            $existingUser->save();

            try {
                $existingUser->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                \Log::error('Verification email failed: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Verification email resent. Please check your email to verify your account.',
                'user'    => [
                    'id'         => $existingUser->id,
                    'name'       => $existingUser->name,
                    'first_name' => $existingUser->first_name,
                    'last_name'  => $existingUser->last_name,
                    'email'      => $existingUser->email,
                    'role'       => $existingUser->role,
                    'verified'   => false,
                ],
            ], 201);
        }

        // Create new user
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->name       = $request->first_name . ' ' . $request->last_name;
        $user->email      = $request->email;
        $user->password   = Hash::make($request->password);
        $user->role       = 'customer';
        $user->phone      = $request->phone ?? null;
        $user->address    = $request->address ?? null;
        $user->save();

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Log::error('Verification email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Registration successful. Please check your email to verify your account.',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'role'       => $user->role,
                'verified'   => false,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return response()->json([
                'message'            => 'Email not verified. Please verify your email before logging in.',
                'email_not_verified' => true,
                'email'              => $user->email,
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Login successful.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'         => $user->id,
                'name'       => $user->name,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'role'       => $user->role,
                'verified'   => true,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        try {
            $user->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification email resent successfully.']);
        } catch (\Exception $e) {
            \Log::error('Resend verification failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send email. Please check mail configuration.'
            ], 500);
        }
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();

        if ($user->role !== 'admin') {
            Auth::logout();
            return response()->json(['message' => 'Access denied. Admins only.'], 403);
        }

        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'message'      => 'Admin login successful.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }
}
