<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceCors
{
    protected array $allowedOrigins = [
        'http://localhost:5173',
        'http://localhost:3000',
        'https://flexispace-frontend-final-production.up.railway.app',
        'https://flexispace-frontend-final.vercel.app',
    ];

    public function handle(Request $request, Closure $next)
    {
        $origin = $request->headers->get('Origin');
        $allowed = $this->resolveOrigin($origin);

        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $allowed)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', $allowed);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }

    private function resolveOrigin(?string $origin): string
    {
        if (!$origin) {
            return $this->allowedOrigins[0];
        }

        // Allow any railway.app or vercel.app subdomain
        if (
            in_array($origin, $this->allowedOrigins) ||
            preg_match('/^https:\/\/.*\.up\.railway\.app$/', $origin) ||
            preg_match('/^https:\/\/.*\.vercel\.app$/', $origin)
        ) {
            return $origin;
        }

        // Allow env-configured FRONTEND_URL
        $envUrl = env('FRONTEND_URL');
        if ($envUrl && $origin === $envUrl) {
            return $origin;
        }

        return $this->allowedOrigins[0];
    }
}
