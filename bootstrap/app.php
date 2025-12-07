<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;          // <--- Add this
use Illuminate\Support\Facades\Auth;  // <--- Add this

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add this logic to handle redirects for logged-in users
        $middleware->redirectUsersTo(function (Request $request) {
            // If the user is logged in as an 'admin', send them to the dashboard
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            // Otherwise, send them to the public homepage (or user dashboard)
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();