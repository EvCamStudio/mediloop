<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $fillable = [
        'transfer_code',
        'donor_facility_id',
        'recipient_facility_id',
        'batch_id',
        'quantity',
        'status',
        'need_score',
        'proximity_score',
        'logistics_score',
        'match_score',
        'distance_km',
        'recipient_daily_burn_rate',
        'recipient_intake_cap',
        'tracking_number',
        'courier_type',
        'notes',
        'rejection_reason',
        'proposed_by',
        'approved_by',
        'accepted_by',
        'completed_by',
        'proposed_at',
        'approved_at',
        'accepted_at',
        'shipped_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'need_score' => 'float',
            'proximity_score' => 'float',
            'logistics_score' => 'float',
            'match_score' => 'float',
            'distance_km' => 'float',
            'proposed_at' => 'datetime',
            'approved_at' => 'datetime',
            'accepted_at' => 'datetime',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Facility, $this>
     */
    public function donorFacility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'donor_facility_id');
    }

    /**
     * @return BelongsTo<Facility, $this>
     */
    public function recipientFacility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'recipient_facility_id');
    }

    /**
     * @return BelongsTo<Batch, $this>
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function proposedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function acceptedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isPendingDonorApproval(): bool
    {
        return $this->status === 'PROPOSED';
    }

    public function isPendingRecipientAcceptance(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isReadyForDispatch(): bool
    {
        return $this->status === 'ACCEPTED';
    }

    public function isInTransit(): bool
    {
        return $this->status === 'IN_TRANSIT';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }
}
