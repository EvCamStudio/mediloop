<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the interface locale for the current request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        app()->setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $supportedLocales = array_keys((array) config('localization.supported', []));
        $candidate = ($request->user() ? $request->user()->locale : null)
            ?? $request->session()->get('locale')
            ?? config('app.locale');

        return in_array($candidate, $supportedLocales, true)
            ? $candidate
            : (string) config('app.locale');
    }
}
