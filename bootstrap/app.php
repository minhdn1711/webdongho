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
    ->withSchedule(function ($schedule) {
        $schedule->command('app:stock-check')->dailyAt('08:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust reverse proxy (NPM/nginx) để HTTPS hoạt động đúng
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureIsAdmin::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/webhooks/pancake'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
