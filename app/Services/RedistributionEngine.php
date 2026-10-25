<?php

namespace App\Services;

use App\Enums\FacilityStatus;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RedistributionEngine
{
    /**
     * Maximum distance (km) at which a recipient still earns a proximity score.
     */
    public const PROXIMITY_MAX_RADIUS_KM = 50.0;

    /**
     * Calculate daily burn rate over an observation window.
     */
    public static function calculateBurnRate(int $dispensedUnits, int $windowDays): float
    {
        if ($windowDays <= 0) {
            throw new InvalidArgumentException('Window days must be greater than zero.');
        }

        return round($dispensedUnits / $windowDays, 2);
    }

    /**
     * Calculate days until current inventory reaches zero.
     */
    public static function calculateDaysToDeplete(int $currentStock, float $burnRate): float
    {
        if ($currentStock <= 0) {
            return 0.0;
        }

        if ($burnRate <= 0.0) {
            return INF;
        }

        return round($currentStock / $burnRate, 1);
    }

    /**
     * Calculate projected unconsumed units at expiration date.
     * Formula: max(0, currentStock - (burnRate * daysToExpiry))
     */
    public static function calculateProjectedWaste(int $currentStock, float $burnRate, int $daysToExpiry): int
    {
        if ($currentStock <= 0 || $daysToExpiry <= 0) {
            return max(0, $currentStock);
        }

        $expectedConsumption = (int) floor($burnRate * $daysToExpiry);
        $projectedWaste = $currentStock - $expectedConsumption;

        return max(0, $projectedWaste);
    }

    /**
     * Determine clinical risk level based on PRD Section 9 thresholds:
     * - CRITICAL: Days to expiry <= 30 OR Waste Ratio >= 50%
     * - HIGH: Days to expiry <= 60 OR Waste Ratio >= 25%
     * - MEDIUM: Days to expiry <= 90 OR Projected Waste > 0
     * - LOW: Adequate burn rate to fully deplete before expiry (Projected Waste = 0)
     */
    public static function determineRiskLevel(int $daysToExpiry, float $wasteRatio, int $projectedWaste): string
    {
        if ($projectedWaste <= 0) {
            return 'LOW';
        }

        // Critical: 30 days or fewer to expiry with unconsumed inventory
        if ($daysToExpiry <= 30) {
            return 'CRITICAL';
        }

        // High: 31-60 days to expiry OR significant projected waste ratio (>= 25%)
        if ($daysToExpiry <= 60 || $wasteRatio >= 0.25) {
            return 'HIGH';
        }

        // Medium: 61-90 days to expiry or any projected waste
        if ($daysToExpiry <= 90 || $projectedWaste > 0) {
            return 'MEDIUM';
        }

        return 'LOW';
    }

    /**
     * Calculate geographical distance between two coordinates using the Haversine formula (km).
     */
    public static function calculateHaversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadiusKm = 6371.0;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 2);
    }

    /**
     * Calculate composite match score:
     * 50% Need + 30% Proximity + 20% Logistics
     */
    public static function calculateMatchingScore(
        float $needScore,
        float $proximityScore,
        float $logisticsScore
    ): float {
        $score = (0.50 * $needScore) + (0.30 * $proximityScore) + (0.20 * $logisticsScore);

        return round(min(100.0, max(0.0, $score)), 1);
    }

    /**
     * Calculate proximity score decaying with distance up to the proximity radius threshold.
     */
    public static function calculateProximityScore(
        float $distanceKm,
        float $maxRadiusKm = self::PROXIMITY_MAX_RADIUS_KM
    ): float {
        if ($distanceKm <= 0.0) {
            return 100.0;
        }

        if ($distanceKm >= $maxRadiusKm) {
            return 0.0;
        }

        return round(max(0.0, 100.0 - (($distanceKm / $maxRadiusKm) * 100.0)), 1);
    }

    /**
     * Calculate recipient intake cap to prevent secondary expiration at the recipient facility.
     * Formula: min(projectedWaste, recipientBurnRate * (daysToExpiry - bufferDays))
     */
    public static function calculateRecipientIntakeCap(
        int $projectedWaste,
        float $recipientBurnRate,
        int $daysToExpiry,
        int $bufferDays = 7
    ): int {
        $usableDays = max(0, $daysToExpiry - $bufferDays);
        $maxAbsorbable = (int) floor($recipientBurnRate * $usableDays);

        return max(0, min($projectedWaste, $maxAbsorbable));
    }

    /**
     * Rank facilities by straight-line distance from an origin facility.
     *
     * @param  Collection<int, Facility>|null  $facilities
     * @return array<int, array{facility: Facility, distance_km: float, proximity_score: float}>
     */
    public static function nearestFacilities(
        Facility $origin,
        ?Collection $facilities = null,
        ?int $limit = null
    ): array {
        $candidates = $facilities ?? Facility::query()->operational()->active()->get();

        $ranked = [];

        foreach ($candidates as $facility) {
            if ($facility->is($origin) || ! self::hasUsableCoordinates($facility)) {
                continue;
            }

            $distanceKm = self::calculateHaversineDistance(
                (float) $origin->latitude,
                (float) $origin->longitude,
                (float) $facility->latitude,
                (float) $facility->longitude
            );

            $ranked[] = [
                'facility' => $facility,
                'distance_km' => $distanceKm,
                'proximity_score' => self::calculateProximityScore($distanceKm),
            ];
        }

        usort($ranked, fn (array $a, array $b): int => $a['distance_km'] <=> $b['distance_km']);

        return $limit === null ? $ranked : array_slice($ranked, 0, max(0, $limit));
    }

    /**
     * Determine whether a facility has coordinates usable for distance calculation.
     */
    private static function hasUsableCoordinates(Facility $facility): bool
    {
        $latitude = $facility->latitude;
        $longitude = $facility->longitude;

        if (! is_finite($latitude) || ! is_finite($longitude)) {
            return false;
        }

        // (0, 0) is treated as an unset coordinate rather than a real facility location.
        if ($latitude === 0.0 && $longitude === 0.0) {
            return false;
        }

        return $latitude >= -90.0 && $latitude <= 90.0
            && $longitude >= -180.0 && $longitude <= 180.0;
    }

    /**
     * Score candidate facilities for an impending waste batch.
     *
     * @param  string  $sort  Either "score" (composite match score) or "distance" (nearest first).
     * @return array<int, array<string, mixed>>
     */
    public static function findCandidateRecipients(Batch $batch, string $sort = 'score'): array
    {
        $donor = $batch->facility;
        $medicine = $batch->medicine;
        $projectedWaste = $batch->projected_waste;
        $daysToExpiry = $batch->days_to_expiry;

        $facilities = Facility::where('id', '!=', $donor->id)
            ->where('type', '!=', 'DINKES')
            ->where('status', FacilityStatus::Active->value)
            ->get();

        $candidates = [];

        foreach ($facilities as $facility) {
            // Cold chain qualification check
            if ($medicine->requires_cold_chain && ! $facility->cold_chain_ready) {
                continue;
            }

            // Calculate distance
            $distanceKm = self::calculateHaversineDistance(
                $donor->latitude,
                $donor->longitude,
                $facility->latitude,
                $facility->longitude
            );

            // Proximity score (30%)
            $proximityScore = self::calculateProximityScore($distanceKm);

            // Query active batches of this medicine (or generic equivalent) at recipient facility
            $genericParts = explode(' ', trim($medicine->generic_name ?? $medicine->name));
            $genericRoot = $genericParts[0];
            $facilityBatches = Batch::where('facility_id', $facility->id)
                ->where(function ($q) use ($medicine, $genericRoot) {
                    $q->where('medicine_id', $medicine->id);
                    if ($genericRoot !== '') {
                        $q->orWhereHas('medicine', function ($mq) use ($genericRoot) {
                            $mq->where('generic_name', 'LIKE', $genericRoot.'%')
                                ->orWhere('name', 'LIKE', $genericRoot.'%');
                        });
                    }
                })
                ->where('status', 'ACTIVE')
                ->get();

            $recipientStock = (int) $facilityBatches->sum('current_stock');
            $existingBatch = $facilityBatches->first();
            $recipientBurnRate = $existingBatch ? (float) $existingBatch->daily_burn_rate : 0.0;

            if ($facilityBatches->isNotEmpty() && $recipientBurnRate > 0) {
                $daysOfStockLeft = self::calculateDaysToDeplete($recipientStock, $recipientBurnRate);

                if ($daysOfStockLeft <= 3) {
                    $needScore = 98.0;
                    $urgencyLabel = "Defisit Kritis: Sisa {$recipientStock} unit ({$daysOfStockLeft} hari pemakaian)";
                    $urgencySeverity = 'CRITICAL';
                } elseif ($daysOfStockLeft <= 7) {
                    $needScore = 90.0;
                    $urgencyLabel = "Stok Menipis: Sisa {$recipientStock} unit ({$daysOfStockLeft} hari pemakaian)";
                    $urgencySeverity = 'HIGH';
                } elseif ($daysOfStockLeft <= 14) {
                    $needScore = 75.0;
                    $urgencyLabel = "Kebutuhan Moderat: Sisa {$recipientStock} unit ({$daysOfStockLeft} hari)";
                    $urgencySeverity = 'MEDIUM';
                } elseif ($daysOfStockLeft <= 30) {
                    $needScore = 45.0;
                    $urgencyLabel = "Stok Cukup: Sisa {$recipientStock} unit ({$daysOfStockLeft} hari)";
                    $urgencySeverity = 'LOW';
                } else {
                    $needScore = 15.0;
                    $urgencyLabel = "Surplus: Sisa stok melimpah ({$daysOfStockLeft} hari)";
                    $urgencySeverity = 'SURPLUS';
                }
            } else {
                // If the facility has no active batch of this medicine:
                $isAppropriate = true;
                if ($facility->type === 'KLINIK' && in_array($medicine->category, ['Endocrine', 'Emergency'], true)) {
                    $isAppropriate = false;
                }

                if ($isAppropriate) {
                    $recipientBurnRate = match ($facility->type) {
                        'RSUD' => 6.0,
                        'RS_SWASTA' => 5.0,
                        'PUSKESMAS' => 3.0,
                        default => 2.0,
                    };
                    $daysOfStockLeft = 0.0;
                    $needScore = 92.0;
                    $urgencyLabel = 'Kekosongan Total: Faskes belum memiliki persediaan obat ini';
                    $urgencySeverity = 'CRITICAL';
                } else {
                    $recipientBurnRate = 0.0;
                    $daysOfStockLeft = INF;
                    $needScore = 0.0;
                    $urgencyLabel = 'Bukan formularium/tidak melayani jenis obat ini';
                    $urgencySeverity = 'INELIGIBLE';
                }
            }

            // Disqualify completely ineligible facilities
            if ($needScore <= 0.0) {
                continue;
            }

            // Logistics score (20%)
            $logisticsScore = $medicine->requires_cold_chain ? 95.0 : 85.0;

            // Composite Match Score (0.50 Need + 0.30 Proximity + 0.20 Logistics)
            $matchScore = self::calculateMatchingScore($needScore, $proximityScore, $logisticsScore);

            // Transfer Cap
            $intakeCap = self::calculateRecipientIntakeCap(
                $projectedWaste,
                $recipientBurnRate,
                $daysToExpiry
            );

            $candidates[] = [
                'facility' => $facility,
                'distance_km' => $distanceKm,
                'need_score' => $needScore,
                'proximity_score' => $proximityScore,
                'logistics_score' => $logisticsScore,
                'match_score' => $matchScore,
                'recipient_daily_burn_rate' => $recipientBurnRate,
                'recipient_stock' => $recipientStock,
                'days_of_stock' => is_finite($daysOfStockLeft) ? $daysOfStockLeft : 0,
                'urgency_label' => $urgencyLabel,
                'urgency_severity' => $urgencySeverity,
                'recipient_intake_cap' => $intakeCap,
                'intake_cap' => $intakeCap,
                'recommended_quantity' => $intakeCap > 0 ? $intakeCap : min(max(10, $projectedWaste), $batch->current_stock),
            ];
        }

        // Rank by the requested order: nearest distance or composite match score.
        if ($sort === 'distance') {
            usort($candidates, fn ($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        } else {
            usort($candidates, fn ($a, $b) => $b['match_score'] <=> $a['match_score']);
        }

        return $candidates;
    }

    /**
     * Complete transfer and execute atomic stock mutation.
     * INVARIANT: Ledger and physical inventory change ONLY upon this execution!
     */
    public static function completeTransfer(Transfer $transfer, User $completer): bool
    {
        if ($transfer->status === 'COMPLETED') {
            return false;
        }

        return DB::transaction(function () use ($transfer, $completer) {
            $donorBatch = Batch::lockForUpdate()->findOrFail($transfer->batch_id);

            if ($donorBatch->current_stock < $transfer->quantity) {
                throw new InvalidArgumentException(
                    "Insufficient donor batch stock. Available: {$donorBatch->current_stock}, requested: {$transfer->quantity}"
                );
            }

            // 1. Deduct donor stock
            $previousDonorStock = $donorBatch->current_stock;
            $donorBatch->current_stock -= $transfer->quantity;
            if ($donorBatch->current_stock === 0) {
                $donorBatch->status = 'DEPLETED';
            }
            $donorBatch->save();

            // 2. Add or create recipient batch
            $recipientBatch = Batch::lockForUpdate()
                ->where('facility_id', $transfer->recipient_facility_id)
                ->where('medicine_id', $donorBatch->medicine_id)
                ->where('batch_number', $donorBatch->batch_number)
                ->first();

            $previousRecipientStock = 0;
            if ($recipientBatch) {
                $previousRecipientStock = $recipientBatch->current_stock;
                $recipientBatch->current_stock += $transfer->quantity;
                $recipientBatch->status = 'ACTIVE';
                $recipientBatch->save();
            } else {
                $recipientBatch = Batch::create([
                    'facility_id' => $transfer->recipient_facility_id,
                    'medicine_id' => $donorBatch->medicine_id,
                    'batch_number' => $donorBatch->batch_number,
                    'current_stock' => $transfer->quantity,
                    'daily_burn_rate' => $transfer->recipient_daily_burn_rate > 0
                        ? $transfer->recipient_daily_burn_rate
                        : $donorBatch->daily_burn_rate,
                    'expiry_date' => $donorBatch->expiry_date,
                    'unit_price_idr' => $donorBatch->unit_price_idr,
                    'storage_location' => 'Gudang Farmasi Penerima',
                    'status' => 'ACTIVE',
                ]);
            }

            // 3. Update transfer status
            $transfer->update([
                'status' => 'COMPLETED',
                'completed_by' => $completer->id,
                'completed_at' => now(),
            ]);

            // 4. Record immutable audit log
            AuditLog::create([
                'actor_id' => $completer->id,
                'facility_id' => $transfer->recipient_facility_id,
                'transfer_id' => $transfer->id,
                'action' => 'TRANSFER_COMPLETED',
                'description' => "Transfer {$transfer->transfer_code} successfully completed. Received {$transfer->quantity} units of {$donorBatch->medicine->name} (Batch: {$donorBatch->batch_number}).",
                'state_delta' => [
                    'donor_facility_id' => $transfer->donor_facility_id,
                    'recipient_facility_id' => $transfer->recipient_facility_id,
                    'quantity' => $transfer->quantity,
                    'donor_stock_before' => $previousDonorStock,
                    'donor_stock_after' => $donorBatch->current_stock,
                    'recipient_stock_before' => $previousRecipientStock,
                    'recipient_stock_after' => $recipientBatch->current_stock,
                ],
                'created_at' => now(),
            ]);

            return true;
        });
    }
}
