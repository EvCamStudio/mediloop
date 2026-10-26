<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilitySettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->facility instanceof Facility) {
            abort(403, __('facility.errors.no_facility'));
        }

        return view('facility.settings', ['facility' => $user->facility]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->facility instanceof Facility) {
            abort(403, __('facility.errors.no_facility'));
        }

        $facility = $user->facility;

        $validated = $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'cold_chain_ready' => ['boolean'],
        ]);

        $facility->update($validated);

        AuditLog::create([
            'actor_id' => $user->id,
            'facility_id' => $facility->id,
            'action' => 'FACILITY_UPDATED',
            'description' => __('facility.audit.contact_updated', ['facility' => $facility->name]),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return back()->with('status', __('facility.settings.updated'));
    }
}
