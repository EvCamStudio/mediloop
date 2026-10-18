<?php

namespace App\Models;

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
 * @property string $role
 * @property string|null $license_sip
 * @property string|null $job_title
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'facility_id', 'role', 'license_sip', 'job_title'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function isDonorStaff(): bool
    {
        return $this->role === 'DONOR_STAFF';
    }

    public function isDonorApprover(): bool
    {
        return in_array($this->role, ['DONOR_APPROVER', 'SYSADMIN']);
    }

    public function isRecipientStaff(): bool
    {
        return $this->role === 'RECIPIENT_STAFF';
    }

    public function isRecipientApprover(): bool
    {
        return in_array($this->role, ['RECIPIENT_APPROVER', 'SYSADMIN']);
    }

    public function isDinkesAuditor(): bool
    {
        return in_array($this->role, ['DINKES_AUDITOR', 'SYSADMIN']);
    }

    public function canApproveDonorTransfer(): bool
    {
        return in_array($this->role, ['DONOR_APPROVER', 'SYSADMIN']);
    }

    public function canAcceptRecipientTransfer(): bool
    {
        return in_array($this->role, ['RECIPIENT_APPROVER', 'RECIPIENT_STAFF', 'SYSADMIN']);
    }

    public function canConfirmPhysicalArrival(): bool
    {
        return in_array($this->role, ['RECIPIENT_APPROVER', 'SYSADMIN']);
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
