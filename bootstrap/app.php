<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class, // Daftarkan alias 'admin'
            'driver' => \App\Http\Middleware\IsDriver::class, // Daftarkan alias 'driver'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {

            // 1. Jika request adalah API / JSON (AJAX), return JSON error
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // 2. Jika request biasa (Web), redirect ke landing page + Buka Modal
            return redirect()->route('landing-page')
                ->with('open_modal', true) // Session untuk trigger JS
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        });
    })->create();
