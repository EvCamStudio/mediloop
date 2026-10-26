<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFacilityIsActive
{
    /**
     * Keep operational features closed while a facility is not verified.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || $user->isSysadmin() || $user->isDinkesAuditor()) {
            return $next($request);
        }

        if ($user->facility && ! $user->facility->isActive()) {
            abort(403, __('http.facility_inactive'));
        }

        return $next($request);
    }
}
