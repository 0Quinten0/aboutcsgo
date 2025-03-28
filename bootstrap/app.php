<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyCsrfTokenCustom;
use App\Http\Middleware\HandleInertiaRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->web([
            HandleInertiaRequests::class, // Register Inertia Middleware
        ]);

        $middleware->validateCsrfTokens(except: [
            'http://127.0.0.1:8000/auth/steam/callback',
            'http://127.0.0.1:8000/votes',
            'https://api.aboutcsgo.com/auth/steam/callback',
            'https://api.aboutcsgo.com/votes',


        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
