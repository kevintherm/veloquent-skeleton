<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->remove(ConvertEmptyStringsToNull::class);
        $middleware->web([
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
        ]);

        $middleware->alias([
            'demo.block' => function ($request, $next) {
                if (env('VELO_IS_DEMO') && $request->is('*/settings') && $request->isMethod('PATCH')) {
                    if ($request->hasAny(['storage', 'email'])) {
                        abort(403, 'Configuration changes are restricted in demo mode.');
                    }
                }

                return $next($request);
            },
        ]);

        $middleware->append('demo.block');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
