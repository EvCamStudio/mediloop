<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    /**
     * Instant 1-click role switcher for demo & jury defense testing.
     */
    public function switchRole(string $role): RedirectResponse
    {
        $roleMap = [
            'donor-staff' => 'donor@mediloop.test',
            'donor-head' => 'donor.head@mediloop.test',
            'recipient-staff' => 'recipient@mediloop.test',
            'recipient-head' => 'recipient.head@mediloop.test',
            'auditor' => 'auditor@mediloop.test',
        ];

        if (! isset($roleMap[$role])) {
            return back()->with('error', 'Role demo tidak valid.');
        }

        $user = User::where('email', $roleMap[$role])->firstOrFail();
        Auth::login($user);

        return redirect()->route('dashboard')->with('status', "Berhasil beralih peran ke: {$user->name} ({$user->job_title})");
    }
}
