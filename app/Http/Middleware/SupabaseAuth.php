<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SupabaseAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Cache the validation response to avoid hitting Supabase on every single request
        $cacheKey = 'supabase_auth_' . hash('sha256', $token);
        
        $supabaseUser = Cache::remember($cacheKey, 60, function () use ($token) {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'apikey' => env('SUPABASE_ANON_KEY')
            ])->get(env('SUPABASE_URL') . '/auth/v1/user');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });

        if (!$supabaseUser || !isset($supabaseUser['email'])) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check if user exists in our DB, if not, create them
        $user = User::where('email', $supabaseUser['email'])->first();

        if (!$user) {
            // First time login for this user after Supabase registration
            $metadata = $supabaseUser['user_metadata'] ?? [];
            
            $user = new User();
            $user->email = $supabaseUser['email'];
            $user->first_name = $metadata['first_name'] ?? 'Unknown';
            $user->last_name = $metadata['last_name'] ?? 'Unknown';
            $user->name = trim(($metadata['first_name'] ?? '') . ' ' . ($metadata['last_name'] ?? ''));
            $user->phone = $metadata['phone'] ?? null;
            $user->address = $metadata['address'] ?? null;
            $user->password = bcrypt(\Illuminate\Support\Str::random(16)); // Random password, not used
            $user->role = 'customer';
            $user->email_verified_at = now(); // Supabase handles verification
            $user->save();
        } elseif (!$user->hasVerifiedEmail()) {
             // If user existed but wasn't verified, mark as verified since they authenticated via Supabase
             $user->email_verified_at = now();
             $user->save();
        }

        // Log the user in for this request
        Auth::setUser($user);

        return $next($request);
    }
}
