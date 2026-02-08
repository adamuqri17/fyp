<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Redirect Admin to Dashboard after Login
        $middleware->redirectUsersTo(function (Request $request) {
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            return '/';
        });

        // DISABLE CSRF FOR TOYYIBPAY CALLBACK
        $middleware->validateCsrfTokens(except: [
            'services/payment/callback', 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();