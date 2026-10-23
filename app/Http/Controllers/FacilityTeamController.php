<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\FacilityInvitation;
use App\Models\User;
use App\Services\FacilityInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacilityTeamController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->facility instanceof Facility) {
            abort(403, 'Akun Anda belum terhubung ke fasilitas.');
        }

        $facility = $user->facility;

        return view('facility.team', [
            'facility' => $facility,
            'members' => $facility->users()->orderBy('name')->get(),
            'invitations' => $facility->invitations()
                ->with('inviter')
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->latest()
                ->get(),
            'roles' => $this->invitableRoles($user),
        ]);
    }

    public function store(Request $request, FacilityInvitationService $invitations): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->facility instanceof Facility) {
            abort(403, 'Akun Anda belum terhubung ke fasilitas.');
        }

        $facility = $user->facility;

        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'string', Rule::in(array_keys($this->invitableRoles($user)))],
        ]);

        $invitations->invite(
            $facility,
            $request->string('email')->trim()->toString(),
            $request->string('role')->toString(),
            $user,
        );

        return back()->with('status', 'Undangan dikirim. Tautan berlaku selama 7 hari.');
    }

    public function destroy(
        Request $request,
        FacilityInvitation $invitation,
        FacilityInvitationService $invitations,
    ): RedirectResponse {
        $user = $request->user();

        if (! $user instanceof User || $invitation->facility_id !== $user->facility_id) {
            abort(403, 'Undangan ini bukan milik fasilitas Anda.');
        }

        $invitations->revoke($invitation, $user);

        return back()->with('status', 'Undangan dibatalkan.');
    }

    /**
     * @return array<string, string>
     */
    private function invitableRoles(User $user): array
    {
        $roles = [
            UserRole::FacilityStaff->value => 'Staf Farmasi',
        ];

        if ($user->isSysadmin()) {
            $roles = [UserRole::FacilityApprover->value => 'Kepala Fasilitas'] + $roles;
        }

        return $roles;
    }
}
