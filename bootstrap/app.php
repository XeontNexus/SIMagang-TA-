<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // If not logged in, redirect to login page
            if (!$request->user()) {
                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            // If logged in, redirect back with error message
            return redirect()->back()
                ->with('error', 'Sesi Anda telah berakhir. Silakan coba lagi.');
        });
    })->create();
