<?php

namespace App\Models;

use App\Services\RedistributionEngine;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'medicine_id',
        'batch_number',
        'current_stock',
        'daily_burn_rate',
        'expiry_date',
        'unit_price_idr',
        'storage_location',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'daily_burn_rate' => 'float',
            'expiry_date' => 'date',
            'unit_price_idr' => 'decimal:2',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    /**
     * Compute days remaining until expiration.
     */
    public function getDaysToExpiryAttribute(): int
    {
        return (int) max(0, Carbon::today()->diffInDays($this->expiry_date, false));
    }

    /**
     * Compute days to deplete based on current stock and daily burn rate.
     */
    public function getDaysToDepleteAttribute(): float
    {
        return RedistributionEngine::calculateDaysToDeplete(
            $this->current_stock,
            $this->daily_burn_rate
        );
    }

    /**
     * Compute projected unconsumed units at expiration date.
     */
    public function getProjectedWasteAttribute(): int
    {
        return RedistributionEngine::calculateProjectedWaste(
            $this->current_stock,
            $this->daily_burn_rate,
            $this->days_to_expiry
        );
    }

    /**
     * Compute waste ratio as a decimal between 0 and 1.
     */
    public function getWasteRatioAttribute(): float
    {
        return $this->current_stock > 0
            ? round($this->projected_waste / $this->current_stock, 4)
            : 0.0;
    }

    /**
     * Compute risk classification: CRITICAL, HIGH, MEDIUM, LOW.
     */
    public function getRiskLevelAttribute(): string
    {
        return RedistributionEngine::determineRiskLevel(
            $this->days_to_expiry,
            $this->waste_ratio,
            $this->projected_waste
        );
    }
}
