<?php

namespace App\Models;

use App\Enums\FacilityStatus;
use App\Enums\FacilityType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $type
 * @property string|null $ownership
 * @property string|null $license_number
 * @property string $address
 * @property string $city
 * @property string|null $province
 * @property string|null $district
 * @property float $latitude
 * @property float $longitude
 * @property bool $cold_chain_ready
 * @property string $contact_person
 * @property string $phone
 * @property string|null $email
 * @property string $status
 * @property Carbon|null $verified_at
 * @property int|null $verified_by
 * @property string|null $notes
 * @property User|null $verifiedBy
 * @property-read int|null $users_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Facility extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => FacilityStatus::Active->value,
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'ownership',
        'license_number',
        'address',
        'city',
        'province',
        'district',
        'latitude',
        'longitude',
        'cold_chain_ready',
        'contact_person',
        'phone',
        'email',
        'status',
        'verified_at',
        'verified_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'cold_chain_ready' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Batch, $this>
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * @return HasMany<Transfer, $this>
     */
    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'donor_facility_id');
    }

    /**
     * @return HasMany<Transfer, $this>
     */
    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'recipient_facility_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * @return HasMany<FacilityInvitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(FacilityInvitation::class);
    }

    /**
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * @return HasMany<FacilityRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(FacilityRequest::class);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', FacilityStatus::Active->value);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOperational(Builder $query): Builder
    {
        return $query->where('type', '!=', FacilityType::Dinkes->value);
    }

    public function isActive(): bool
    {
        return $this->status === FacilityStatus::Active->value;
    }

    public function getStatusLabelAttribute(): string
    {
        return FacilityStatus::tryFrom((string) $this->status)?->label() ?? (string) $this->status;
    }

    public function getTypeLabelAttribute(): string
    {
        return FacilityType::tryFrom((string) $this->type)?->label() ?? (string) $this->type;
    }
}
