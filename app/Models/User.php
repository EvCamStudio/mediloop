<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int|null $facility_id
 * @property Facility|null $facility
 * @property string $role
 * @property string $status
 * @property int|null $invited_by
 * @property Carbon|null $joined_at
 * @property string|null $license_sip
 * @property string|null $job_title
 * @property string|null $locale
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'facility_id', 'role', 'status', 'invited_by', 'joined_at', 'license_sip', 'job_title', 'locale'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected $attributes = [
        'status' => UserStatus::Active->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'joined_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'invited_by');
    }

    public function isSysadmin(): bool
    {
        return $this->role === UserRole::Sysadmin->value;
    }

    /**
     * Regional oversight access. The Health Agency auditor role is merged into
     * the platform administrator role, so legacy DINKES_AUDITOR accounts keep
     * their read-only regional visibility without gaining admin write access.
     */
    public function isDinkesAuditor(): bool
    {
        return in_array($this->role, [
            UserRole::DinkesAuditor->value,
            UserRole::Sysadmin->value,
        ], true);
    }

    /**
     * The facility approver role is merged into the facility staff role, which
     * owns the whole redistribution lifecycle for its own facility.
     */
    public function isFacilityApprover(): bool
    {
        return $this->isFacilityStaff();
    }

    public function isFacilityStaff(): bool
    {
        return in_array($this->role, [
            UserRole::FacilityApprover->value,
            UserRole::FacilityStaff->value,
        ], true);
    }

    public function hasFacilityRole(): bool
    {
        return $this->isFacilityStaff();
    }

    /**
     * Facility settings and team management edit the user's own facility, so
     * they belong to the facility role. Platform administrators keep access so
     * existing admin tooling is unaffected.
     */
    public function canManageFacility(): bool
    {
        return $this->isSysadmin() || $this->hasFacilityRole();
    }

    public function canApproveDonorTransfer(): bool
    {
        return $this->isSysadmin() || $this->hasFacilityRole();
    }

    public function canAcceptRecipientTransfer(): bool
    {
        return $this->isSysadmin() || $this->hasFacilityRole();
    }

    public function canConfirmPhysicalArrival(): bool
    {
        return $this->isSysadmin() || $this->hasFacilityRole();
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active->value;
    }

    public function isSuspended(): bool
    {
        return $this->status === UserStatus::Suspended->value;
    }

    public function getRoleLabelAttribute(): string
    {
        return UserRole::tryFrom((string) $this->role)?->label() ?? (string) $this->role;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
