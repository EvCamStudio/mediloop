<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display deep statistical calculations and interactive visual data science analytics.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $facilityId = $user->facility_id;

        if (($user->isDinkesAuditor() || $user->role === 'SYSADMIN') && $request->filled('facility_id')) {
            $facilityId = (int) $request->get('facility_id');
        }

        $facility = Facility::find($facilityId);

        $query = Batch::with(['medicine', 'facility'])
            ->where('status', 'ACTIVE');

        if ($facilityId && ! $user->isDinkesAuditor()) {
            $query->where('facility_id', $facilityId);
        } elseif ($request->filled('facility_id')) {
            $query->where('facility_id', $request->get('facility_id'));
        }

        $batches = $query->get();

        // 1. Risk Breakdown (Donut Chart Data)
        $riskCounts = [
            'CRITICAL' => 0,
            'HIGH' => 0,
            'MEDIUM' => 0,
            'LOW' => 0,
        ];

        foreach ($batches as $b) {
            $level = $b->risk_level;
            if (isset($riskCounts[$level])) {
                $riskCounts[$level]++;
            } else {
                $riskCounts['LOW']++;
            }
        }

        $totalBatches = $batches->count();

        // 2. Expiry Horizon Analysis (Batches expiring within buckets)
        $now = now();
        $expiryBuckets = [
            'Darurat (< 30 Hari)' => 0,
            'Waspada (31 - 60 Hari)' => 0,
            'Perhatian (61 - 90 Hari)' => 0,
            'Aman (> 90 Hari)' => 0,
        ];

        foreach ($batches as $b) {
            $days = $b->days_to_expiry;
            if ($days <= 30) {
                $expiryBuckets['Darurat (< 30 Hari)']++;
            } elseif ($days <= 60) {
                $expiryBuckets['Waspada (31 - 60 Hari)']++;
            } elseif ($days <= 90) {
                $expiryBuckets['Perhatian (61 - 90 Hari)']++;
            } else {
                $expiryBuckets['Aman (> 90 Hari)']++;
            }
        }

        // 3. Category Breakdown: Stock vs Projected Waste Value (Bar Chart Data)
        $categoryData = [];
        foreach ($batches as $b) {
            $cat = $b->medicine->category ?? 'Lain-lain';
            if (! isset($categoryData[$cat])) {
                $categoryData[$cat] = [
                    'stock' => 0,
                    'projected_waste_units' => 0,
                    'projected_waste_value_idr' => 0,
                ];
            }
            $categoryData[$cat]['stock'] += $b->current_stock;
            $categoryData[$cat]['projected_waste_units'] += $b->projected_waste;
            $categoryData[$cat]['projected_waste_value_idr'] += ($b->projected_waste * (float) $b->unit_price_idr);
        }

        // Sort categories by highest projected waste value
        uasort($categoryData, fn ($a, $b) => $b['projected_waste_value_idr'] <=> $a['projected_waste_value_idr']);

        // 4. Burn Rate Efficiency Index (Top 6 medicines with slowest burn rate vs impending expiry)
        $burnRateAnomalies = $batches->filter(fn ($b) => $b->daily_burn_rate > 0)
            ->map(function ($b) {
                $daysToDeplete = $b->days_to_deplete;
                $daysToExpiry = max(1, $b->days_to_expiry);
                $surplusRatio = round($daysToDeplete / $daysToExpiry, 2);

                return [
                    'medicine_name' => $b->medicine->name,
                    'batch_number' => $b->batch_number,
                    'current_stock' => $b->current_stock,
                    'daily_burn_rate' => $b->daily_burn_rate,
                    'days_to_deplete' => $daysToDeplete,
                    'days_to_expiry' => $daysToExpiry,
                    'surplus_ratio' => $surplusRatio,
                    'projected_waste' => $b->projected_waste,
                    'waste_value' => $b->projected_waste * (float) $b->unit_price_idr,
                ];
            })
            ->sortByDesc('surplus_ratio')
            ->take(6)
            ->values();

        // 5. Aggregate KPIs
        $totalStockUnits = $batches->sum('current_stock');
        $totalWasteUnits = $batches->sum('projected_waste');
        $totalWasteValueIdr = $batches->sum(fn ($b) => $b->projected_waste * (float) $b->unit_price_idr);
        $totalInventoryValueIdr = $batches->sum(fn ($b) => $b->current_stock * (float) $b->unit_price_idr);

        $wasteRatio = $totalStockUnits > 0 ? round(($totalWasteUnits / $totalStockUnits) * 100, 1) : 0;
        $efficiencyScore = 100 - $wasteRatio;

        // 6. Redistribution Impact (Completed Transfers)
        $completedTransfers = Transfer::where('status', 'COMPLETED')
            ->when($facilityId && ! $user->isDinkesAuditor(), function ($q) use ($facilityId) {
                $q->where(function ($sub) use ($facilityId) {
                    $sub->where('donor_facility_id', $facilityId)
                        ->orWhere('recipient_facility_id', $facilityId);
                });
            })
            ->get();

        $savedUnits = $completedTransfers->sum('quantity');
        $savedValueIdr = $completedTransfers->sum(fn ($t) => $t->quantity * (float) $t->batch->unit_price_idr);

        $facilitiesList = Facility::where('type', '!=', 'DINKES')->get();

        return view('analytics.index', [
            'user' => $user,
            'facility' => $facility,
            'facilitiesList' => $facilitiesList,
            'totalBatches' => $totalBatches,
            'riskCounts' => $riskCounts,
            'expiryBuckets' => $expiryBuckets,
            'categoryData' => $categoryData,
            'burnRateAnomalies' => $burnRateAnomalies,
            'totalStockUnits' => $totalStockUnits,
            'totalWasteUnits' => $totalWasteUnits,
            'totalWasteValueIdr' => $totalWasteValueIdr,
            'totalInventoryValueIdr' => $totalInventoryValueIdr,
            'wasteRatio' => $wasteRatio,
            'efficiencyScore' => $efficiencyScore,
            'savedUnits' => $savedUnits,
            'savedValueIdr' => $savedValueIdr,
        ]);
    }
}
