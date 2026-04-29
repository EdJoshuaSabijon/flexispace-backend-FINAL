<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS must come first for API routes
        $middleware->api(prepend: \Illuminate\Http\Middleware\HandleCors::class);

        // CSRF exceptions for API endpoints - add your Railway and Vercel domains
        $middleware->validateCsrfTokens(except: [
            'http://localhost:5173',
            'http://localhost:3000',
            'https://*.railway.app',
            'https://*.vercel.app',
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
