<?php

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectTo(
            guests: function (Request $request) {
                if ($request->is(config('velo.api_prefix', 'api') . '/*')) {
                    return null;
                }

                return route('login');
            },
        );
        $middleware->remove(ConvertEmptyStringsToNull::class);
        $middleware->web([
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is(config('velo.api_prefix', 'api') . '/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })->create();
