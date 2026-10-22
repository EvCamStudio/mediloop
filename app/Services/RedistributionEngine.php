<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RedistributionEngine
{
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
     * Calculate proximity score decaying with distance up to a 50km threshold.
     */
    public static function calculateProximityScore(float $distanceKm, float $maxRadiusKm = 50.0): float
    {
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
     * Score candidate facilities for an impending waste batch.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function findCandidateRecipients(Batch $batch): array
    {
        $donor = $batch->facility;
        $medicine = $batch->medicine;
        $projectedWaste = $batch->projected_waste;
        $daysToExpiry = $batch->days_to_expiry;

        $facilities = Facility::where('id', '!=', $donor->id)
            ->where('type', '!=', 'DINKES')
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

            // Need score (50%) based on recipient stock presence and burn rate
            $existingBatch = Batch::where('facility_id', $facility->id)
                ->where('medicine_id', $medicine->id)
                ->where('status', 'ACTIVE')
                ->first();

            $recipientBurnRate = $existingBatch ? (float) $existingBatch->daily_burn_rate : 5.0; // Simulated demand if unstocked
            $recipientStock = $existingBatch ? $existingBatch->current_stock : 0;

            // Shortage urgency: lower stock relative to burn rate yields higher need score
            $daysOfStockLeft = self::calculateDaysToDeplete($recipientStock, $recipientBurnRate);
            $needScore = $daysOfStockLeft <= 7 ? 95.0 : ($daysOfStockLeft <= 14 ? 80.0 : ($daysOfStockLeft <= 30 ? 60.0 : 35.0));

            // Logistics score (20%)
            $logisticsScore = $facility->cold_chain_ready ? 90.0 : 70.0;

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
                'recipient_intake_cap' => $intakeCap,
                'intake_cap' => $intakeCap,
                'recommended_quantity' => $intakeCap > 0 ? $intakeCap : min($projectedWaste, 100),
            ];
        }

        // Sort descending by match score
        usort($candidates, fn ($a, $b) => $b['match_score'] <=> $a['match_score']);

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
