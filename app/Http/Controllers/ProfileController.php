<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
            'current_password' => 'sometimes|string|min:6',
            'password' => 'sometimes|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update profile fields
        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }
        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }

        // Update password if provided
        if ($request->has('password')) {
            if (!$request->has('current_password')) {
                return response()->json([
                    'message' => 'Current password is required to change password',
                    'errors' => ['current_password' => ['Current password is required']]
                ], 422);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                    'errors' => ['current_password' => ['Current password is incorrect']]
                ], 422);
            }

            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        // Prevent admin accounts from being deleted
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Admin accounts cannot be deleted'
            ], 403);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Delete the user
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully'
        ]);
    }
}
