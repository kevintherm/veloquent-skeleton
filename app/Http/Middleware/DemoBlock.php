<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoBlock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (env('VELO_IS_DEMO') && $request->is('*/settings') && $request->isMethod('PATCH')) {
            if ($request->hasAny(['storage', 'email'])) {
                abort(403, 'Configuration changes are restricted in demo mode.');
            }
        }

        return $next($request);
    }
}
