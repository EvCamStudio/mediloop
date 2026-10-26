<?php

namespace App\Http\Middleware;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        try {
            Carbon::setLocale($locale);
        } catch (\Exception $e) {
            // Locale not installed on server; keep default
        }
        try {
            CarbonImmutable::setLocale($locale);
        } catch (\Exception $e) {
            // Locale not installed on server; keep default
        }

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
