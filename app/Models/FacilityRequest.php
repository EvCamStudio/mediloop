<?php

namespace App\Models;

use App\Enums\FacilityRequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property string $email
 * @property string|null $notes
 * @property string $status
 * @property int|null $facility_id
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property string|null $rejection_reason
 * @property Facility|null $facility
 * @property User|null $reviewer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class FacilityRequest extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

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
        'notes',
        'status',
        'facility_id',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'cold_chain_ready' => 'boolean',
            'reviewed_at' => 'datetime',
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
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === FacilityRequestStatus::Pending->value;
    }

    public function getStatusLabelAttribute(): string
    {
        return FacilityRequestStatus::tryFrom((string) $this->status)?->label() ?? (string) $this->status;
    }
}
