<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFacilityApprover
{
    /**
     * Restrict facility team management to approvers and platform admins.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->canManageFacility()) {
            abort(403, __('http.facility_approver_only'));
        }

        return $next($request);
    }
}
