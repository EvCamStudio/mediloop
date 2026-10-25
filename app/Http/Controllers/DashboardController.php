<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use App\Services\RedistributionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display inventory batches and expiry risk analysis.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isAuditor = $user->isDinkesAuditor();
        $requestedFacilityId = $request->filled('facility_id') ? $request->integer('facility_id') : null;
        $facilityId = $isAuditor && $requestedFacilityId ? $requestedFacilityId : $user->facility_id;

        // Accounts without a verified facility must not see cross-facility inventory.
        if (! $isAuditor && ! $facilityId) {
            return view('dashboard', [
                'user' => $user,
                'facility' => null,
                'facilityMissing' => true,
                'batches' => collect(),
                'overviewBatches' => collect(),
                'totalStockUnits' => 0,
                'atRiskCount' => 0,
                'criticalCount' => 0,
                'highCount' => 0,
                'mediumCount' => 0,
                'lowCount' => 0,
                'totalProjectedWasteUnits' => 0,
                'totalProjectedWasteValue' => 0,
                'activeTransfersCount' => 0,
                'facilitiesList' => collect(),
                'topRiskBatch' => null,
                'selectedBatch' => null,
                'matchCandidates' => [],
                'batchRecommendations' => [],
                'partnerFacilities' => [],
            ]);
        }

        $facility = $facilityId ? Facility::find($facilityId) : null;

        $query = Batch::with(['medicine', 'facility'])
            ->where('status', 'ACTIVE');

        if (! $isAuditor) {
            $query->where('facility_id', $facilityId);
        } elseif ($requestedFacilityId) {
            $query->where('facility_id', $requestedFacilityId);
        }

        $batches = $query->orderBy('expiry_date', 'asc')->get();

        // Calculate aggregate metrics
        $totalStockUnits = $batches->sum('current_stock');
        $criticalCount = $batches->where('risk_level', 'CRITICAL')->count();
        $highCount = $batches->where('risk_level', 'HIGH')->count();
        $mediumCount = $batches->where('risk_level', 'MEDIUM')->count();
        $lowCount = $batches->where('risk_level', 'LOW')->count();

        // At-risk batches for overview table (CRITICAL, HIGH, MEDIUM)
        $overviewBatches = $batches->filter(fn ($b) => in_array($b->risk_level, ['CRITICAL', 'HIGH', 'MEDIUM']));
        $atRiskCount = $overviewBatches->count();

        $totalProjectedWasteUnits = $batches->sum('projected_waste');
        $totalProjectedWasteValue = $batches->sum(fn ($b) => $b->projected_waste * (float) $b->unit_price_idr);

        $activeTransfersCount = Transfer::where(function ($q) use ($facilityId) {
            $q->where('donor_facility_id', $facilityId)
                ->orWhere('recipient_facility_id', $facilityId);
        })
            ->whereIn('status', ['PROPOSED', 'APPROVED', 'ACCEPTED', 'IN_TRANSIT'])
            ->count();

        $facilitiesList = Facility::where('type', '!=', 'DINKES')->where('status', 'ACTIVE')->get();

        // Selected batch for candidate recommendations: prioritize requested batch, then CRITICAL, then HIGH, then MEDIUM
        $requestedBatchId = $request->integer('batch_id');
        $selectedBatch = null;
        if ($requestedBatchId > 0) {
            $selectedBatch = $batches->firstWhere('id', $requestedBatchId);
        }

        $topRiskBatch = $selectedBatch
            ?? $batches->firstWhere('risk_level', 'CRITICAL')
            ?? $batches->firstWhere('risk_level', 'HIGH')
            ?? $batches->firstWhere('risk_level', 'MEDIUM')
            ?? $batches->first();

        // Calculate recommendations for all at-risk batches in overview
        $batchRecommendations = [];
        $candidatesBatches = $overviewBatches->isNotEmpty() ? $overviewBatches : $batches;
        foreach ($candidatesBatches as $b) {
            $candidates = RedistributionEngine::findCandidateRecipients($b);
            $batchRecommendations[$b->id] = [
                'batch' => $b,
                'candidates' => array_slice($candidates, 0, 3),
            ];
        }

        $matchCandidates = $topRiskBatch ? ($batchRecommendations[$topRiskBatch->id]['candidates'] ?? []) : [];

        // Calculate real partner facilities relative to current facility
        $partnerFacilities = [];
        if ($facility) {
            foreach ($facilitiesList as $partner) {
                if ($partner->id === $facility->id) {
                    continue;
                }
                $dist = RedistributionEngine::calculateHaversineDistance(
                    $facility->latitude,
                    $facility->longitude,
                    $partner->latitude,
                    $partner->longitude
                );
                $partnerFacilities[] = [
                    'facility' => $partner,
                    'distance_km' => $dist,
                ];
            }
            usort($partnerFacilities, fn ($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        }

        return view('dashboard', [
            'user' => $user,
            'facility' => $facility,
            'batches' => $batches,
            'overviewBatches' => $overviewBatches,
            'totalStockUnits' => $totalStockUnits,
            'atRiskCount' => $atRiskCount,
            'criticalCount' => $criticalCount,
            'highCount' => $highCount,
            'mediumCount' => $mediumCount,
            'lowCount' => $lowCount,
            'totalProjectedWasteUnits' => $totalProjectedWasteUnits,
            'totalProjectedWasteValue' => $totalProjectedWasteValue,
            'activeTransfersCount' => $activeTransfersCount,
            'facilitiesList' => $facilitiesList,
            'topRiskBatch' => $topRiskBatch,
            'selectedBatch' => $topRiskBatch,
            'matchCandidates' => $matchCandidates,
            'batchRecommendations' => $batchRecommendations,
            'partnerFacilities' => $partnerFacilities,
        ]);
    }
}
