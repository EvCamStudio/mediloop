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
                'totalStockUnits' => 0,
                'atRiskCount' => 0,
                'totalProjectedWasteUnits' => 0,
                'totalProjectedWasteValue' => 0,
                'activeTransfersCount' => 0,
                'facilitiesList' => collect(),
                'topRiskBatch' => null,
                'matchCandidates' => [],
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
        $atRiskBatches = $batches->filter(fn ($b) => in_array($b->risk_level, ['CRITICAL', 'HIGH']));
        $totalProjectedWasteUnits = $batches->sum('projected_waste');
        $totalProjectedWasteValue = $batches->sum(fn ($b) => $b->projected_waste * (float) $b->unit_price_idr);

        $activeTransfersCount = Transfer::where(function ($q) use ($facilityId) {
            $q->where('donor_facility_id', $facilityId)
                ->orWhere('recipient_facility_id', $facilityId);
        })
            ->whereIn('status', ['PROPOSED', 'APPROVED', 'ACCEPTED', 'IN_TRANSIT'])
            ->count();

        $facilitiesList = Facility::where('type', '!=', 'DINKES')->get();

        // Top at-risk batch for candidate recommendations
        $topRiskBatch = $atRiskBatches->first() ?? $batches->first();
        $matchCandidates = [];
        if ($topRiskBatch) {
            $matchCandidates = array_slice(RedistributionEngine::findCandidateRecipients($topRiskBatch), 0, 3);
        }

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
            'totalStockUnits' => $totalStockUnits,
            'atRiskCount' => $atRiskBatches->count(),
            'totalProjectedWasteUnits' => $totalProjectedWasteUnits,
            'totalProjectedWasteValue' => $totalProjectedWasteValue,
            'activeTransfersCount' => $activeTransfersCount,
            'facilitiesList' => $facilitiesList,
            'topRiskBatch' => $topRiskBatch,
            'matchCandidates' => $matchCandidates,
            'partnerFacilities' => $partnerFacilities,
        ]);
    }
}
