<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Mail\FacilityInvitationMail;
use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\FacilityInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class FacilityInvitationService
{
    public function invite(Facility $facility, string $email, string $role, User $inviter): FacilityInvitation
    {
        $email = Str::lower(trim($email));

        Validator::make(
            ['email' => $email, 'role' => $role],
            [
                'email' => ['required', 'email', 'max:255', Rule::unique(User::class)],
                'role' => ['required', 'string', Rule::in(UserRole::facilityValues())],
            ],
            [
                'email.unique' => __('invitations.errors.email_taken'),
            ],
        )->validate();

        if (! $facility->isActive()) {
            throw ValidationException::withMessages([
                'email' => __('invitations.errors.facility_inactive'),
            ]);
        }

        return DB::transaction(function () use ($facility, $email, $role, $inviter): FacilityInvitation {
            FacilityInvitation::query()
                ->where('facility_id', $facility->id)
                ->where('email', $email)
                ->whereNull('accepted_at')
                ->delete();

            $plainToken = Str::random(64);

            $invitation = FacilityInvitation::create([
                'facility_id' => $facility->id,
                'email' => $email,
                'role' => $role,
                'token' => FacilityInvitation::hashToken($plainToken),
                'invited_by' => $inviter->id,
                'expires_at' => now()->addDays(7),
            ]);

            Mail::to($email)->send(new FacilityInvitationMail($invitation, $plainToken, $facility));

            $this->log(
                $inviter,
                'TEAM_INVITED',
                __('invitations.audit.invited', ['email' => $email, 'facility' => $facility->name]),
                $facility,
            );

            return $invitation;
        });
    }

    public function accept(string $plainToken, string $name, string $password): User
    {
        $invitation = $this->findUsableInvitation($plainToken);

        Validator::make(['name' => $name, 'password' => $password], [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', Password::defaults()],
        ])->validate();

        if (User::query()->where('email', $invitation->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => __('invitations.errors.email_taken'),
            ]);
        }

        return DB::transaction(function () use ($invitation, $name, $password): User {
            $facility = $invitation->facility()->firstOrFail();

            $user = User::create([
                'name' => $name,
                'email' => $invitation->email,
                'password' => $password,
                'facility_id' => $facility->id,
                'role' => $invitation->role,
                'status' => UserStatus::Active->value,
                'invited_by' => $invitation->invited_by,
                'joined_at' => now(),
                'email_verified_at' => now(),
            ]);

            $invitation->update(['accepted_at' => now()]);

            $this->log(
                $user,
                'INVITATION_ACCEPTED',
                __('invitations.audit.joined', ['name' => $user->name, 'facility' => $facility->name]),
                $facility,
            );

            return $user;
        });
    }

    public function revoke(FacilityInvitation $invitation, User $actor): void
    {
        $facility = $invitation->facility()->firstOrFail();

        $invitation->delete();

        $this->log(
            $actor,
            'INVITATION_REVOKED',
            __('invitations.audit.revoked', ['email' => $invitation->email]),
            $facility,
        );
    }

    public function findUsableInvitation(string $plainToken): FacilityInvitation
    {
        $invitation = FacilityInvitation::query()
            ->with(['facility', 'inviter'])
            ->where('token', FacilityInvitation::hashToken($plainToken))
            ->first();

        if (! $invitation) {
            throw ValidationException::withMessages([
                'token' => __('invitations.errors.not_found'),
            ]);
        }

        if ($invitation->isAccepted()) {
            throw ValidationException::withMessages([
                'token' => __('invitations.errors.already_used'),
            ]);
        }

        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'token' => __('invitations.errors.expired'),
            ]);
        }

        if (! $invitation->facility->isActive()) {
            throw ValidationException::withMessages([
                'token' => __('invitations.errors.facility_inactive_token'),
            ]);
        }

        return $invitation;
    }

    private function log(User $actor, string $action, string $description, Facility $facility): void
    {
        AuditLog::create([
            'actor_id' => $actor->id,
            'facility_id' => $facility->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
