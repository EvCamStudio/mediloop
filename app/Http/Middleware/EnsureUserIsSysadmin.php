<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSysadmin
{
    /**
     * Restrict platform administration to SYSADMIN accounts.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isSysadmin()) {
            abort(403, 'Halaman ini hanya untuk administrator platform.');
        }

        return $next($request);
    }
}
