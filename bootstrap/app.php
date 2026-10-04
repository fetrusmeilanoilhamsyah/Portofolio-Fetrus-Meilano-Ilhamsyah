<?php

use App\Http\Middleware\CacheGuestResponse;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\StripGuestCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->replace(
            TrustProxies::class,
            App\Http\Middleware\TrustProxies::class
        );

        $middleware->validateCsrfTokens(except: [
            'api/csp-report',
        ]);

        $middleware->web(prepend: [
            StripGuestCookies::class,
        ], append: [
            SecurityHeaders::class,
            CacheGuestResponse::class,
        ]);
        $middleware->api(append: [
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
