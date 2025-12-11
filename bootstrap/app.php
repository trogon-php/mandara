<?php

use App\Support\ApiExceptionFormatter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Apply ForceJsonResponse only to API routes
        $middleware->appendToGroup('api', \App\Http\Middleware\ForceJsonResponse::class);
        // REMOVE IsUserActive from global API middleware - it should only be applied to protected routes
        // $middleware->appendToGroup('api', \App\Http\Middleware\IsUserActive::class);

        // Register the middleware with an alias for use in routes
        $middleware->alias([
            'user.active' => \App\Http\Middleware\IsUserActive::class,
            'jwt.validate' => \App\Http\Middleware\JwtAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiExceptionFormatter::format($e);
            }
        });
    })->create();
