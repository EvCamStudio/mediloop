<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * Regional Dinkes oversight dashboard & immutable audit trail.
     */
    public function index(): View
    {
        $user = Auth::user();

        $completedTransfers = Transfer::with(['donorFacility', 'recipientFacility', 'batch.medicine'])
            ->where('status', 'COMPLETED')
            ->get();

        $totalUnitsSaved = $completedTransfers->sum('quantity');
        $totalValueSavedIdr = $completedTransfers->sum(fn ($t) => $t->quantity * (float) $t->batch->unit_price_idr);

        $facilitiesCount = Facility::where('type', '!=', 'DINKES')->count();
        $totalActiveBatches = Batch::where('status', 'ACTIVE')->count();

        $auditLogs = AuditLog::with(['actor', 'facility', 'transfer'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('audit.index', [
            'user' => $user,
            'completedTransfers' => $completedTransfers,
            'totalUnitsSaved' => $totalUnitsSaved,
            'totalValueSavedIdr' => $totalValueSavedIdr,
            'facilitiesCount' => $facilitiesCount,
            'totalActiveBatches' => $totalActiveBatches,
            'auditLogs' => $auditLogs,
        ]);
    }
}
