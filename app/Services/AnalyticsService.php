<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Transfer;
use Illuminate\Support\Collection;

final class AnalyticsService
{
    /**
     * Build the full analytics payload from scoped batches and completed transfers.
     *
     * @param  Collection<int, Batch>  $batches
     * @param  Collection<int, Transfer>  $completedTransfers
     * @return array<string, mixed>
     */
    public static function summarize(Collection $batches, Collection $completedTransfers): array
    {
        return [
            'kpis' => self::kpis($batches, $completedTransfers),
            'riskCounts' => self::riskDistribution($batches),
            'expiryBuckets' => self::expiryHorizon($batches),
            'categories' => self::categoryBreakdown($batches),
            'surplus' => self::surplusAnomalies($batches),
        ];
    }

    /**
     * @param  Collection<int, Batch>  $batches
     * @param  Collection<int, Transfer>  $completedTransfers
     * @return array<string, int|float>
     */
    public static function kpis(Collection $batches, Collection $completedTransfers): array
    {
        $stockUnits = (int) $batches->sum('current_stock');
        $wasteUnits = (int) $batches->sum(fn (Batch $batch): int => $batch->projected_waste);
        $inventoryValue = (float) $batches->sum(
            fn (Batch $batch): float => $batch->current_stock * (float) $batch->unit_price_idr
        );
        $wasteValue = (float) $batches->sum(
            fn (Batch $batch): float => $batch->projected_waste * (float) $batch->unit_price_idr
        );
        $wasteRatio = $stockUnits > 0 ? round(($wasteUnits / $stockUnits) * 100, 1) : 0.0;

        return [
            'batch_count' => $batches->count(),
            'stock_units' => $stockUnits,
            'inventory_value' => $inventoryValue,
            'waste_units' => $wasteUnits,
            'waste_value' => $wasteValue,
            'waste_ratio' => $wasteRatio,
            'efficiency_score' => round(100 - $wasteRatio, 1),
            'saved_units' => (int) $completedTransfers->sum('quantity'),
            'saved_value' => (float) $completedTransfers->sum(
                fn (Transfer $transfer): float => $transfer->quantity * (float) ($transfer->batch->unit_price_idr ?? 0)
            ),
        ];
    }

    /**
     * @param  Collection<int, Batch>  $batches
     * @return array<string, int>
     */
    public static function riskDistribution(Collection $batches): array
    {
        $counts = ['CRITICAL' => 0, 'HIGH' => 0, 'MEDIUM' => 0, 'LOW' => 0];

        foreach ($batches as $batch) {
            $level = (string) $batch->risk_level;
            $key = array_key_exists($level, $counts) ? $level : 'LOW';
            $counts[$key]++;
        }

        return $counts;
    }

    /**
     * @param  Collection<int, Batch>  $batches
     * @return array<string, int>
     */
    public static function expiryHorizon(Collection $batches): array
    {
        $buckets = ['under_30' => 0, 'days_31_60' => 0, 'days_61_90' => 0, 'over_90' => 0];

        foreach ($batches as $batch) {
            $days = $batch->days_to_expiry;

            if ($days <= 30) {
                $buckets['under_30']++;
            } elseif ($days <= 60) {
                $buckets['days_31_60']++;
            } elseif ($days <= 90) {
                $buckets['days_61_90']++;
            } else {
                $buckets['over_90']++;
            }
        }

        return $buckets;
    }

    /**
     * @param  Collection<int, Batch>  $batches
     * @return array<int, array<string, int|float|string>>
     */
    public static function categoryBreakdown(Collection $batches): array
    {
        $categories = [];

        foreach ($batches as $batch) {
            $name = (string) ($batch->medicine?->category ?: 'Unknown');

            $categories[$name] ??= ['name' => $name, 'stock' => 0, 'waste_units' => 0, 'waste_value' => 0.0];
            $categories[$name]['stock'] += $batch->current_stock;
            $categories[$name]['waste_units'] += $batch->projected_waste;
            $categories[$name]['waste_value'] += $batch->projected_waste * (float) $batch->unit_price_idr;
        }

        uasort($categories, fn (array $left, array $right): int => $right['waste_value'] <=> $left['waste_value']);

        return array_values($categories);
    }

    /**
     * @param  Collection<int, Batch>  $batches
     * @return array<int, array<string, int|float|string>>
     */
    public static function surplusAnomalies(Collection $batches, int $limit = 4): array
    {
        return $batches
            ->filter(fn (Batch $batch): bool => $batch->daily_burn_rate > 0)
            ->map(function (Batch $batch): array {
                $daysToExpiry = max(1, $batch->days_to_expiry);

                return [
                    'medicine_name' => (string) $batch->medicine?->name,
                    'batch_number' => $batch->batch_number,
                    'current_stock' => $batch->current_stock,
                    'daily_burn_rate' => $batch->daily_burn_rate,
                    'days_to_deplete' => $batch->days_to_deplete,
                    'days_to_expiry' => $daysToExpiry,
                    'surplus_ratio' => round($batch->days_to_deplete / $daysToExpiry, 1),
                    'projected_waste' => $batch->projected_waste,
                    'waste_value' => $batch->projected_waste * (float) $batch->unit_price_idr,
                ];
            })
            ->sortByDesc('surplus_ratio')
            ->take($limit)
            ->values()
            ->all();
    }
}
