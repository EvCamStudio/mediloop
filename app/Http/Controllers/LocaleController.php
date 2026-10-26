<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    /**
     * Persist the visitor's interface language preference.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(array_keys((array) config('localization.supported')))],
        ]);

        $locale = (string) $validated['locale'];
        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        $user = $request->user();

        if ($user instanceof User) {
            $user->forceFill(['locale' => $locale])->save();
        }

        return back();
    }
}
