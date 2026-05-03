<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ForceCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Force CORS on all routes (handles preflight and error responses)
        $middleware->prepend(ForceCors::class);

        // CSRF exceptions for API endpoints
        $middleware->validateCsrfTokens(except: ['api/*']);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'supabase' => \App\Http\Middleware\SupabaseAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
