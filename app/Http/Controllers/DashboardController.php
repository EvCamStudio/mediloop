<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
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
        $facilityId = $user->facility_id;

        // Allow Dinkes Auditor or Admin to view any facility
        if ($user->isDinkesAuditor() && $request->has('facility_id')) {
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
        ]);
    }
}
