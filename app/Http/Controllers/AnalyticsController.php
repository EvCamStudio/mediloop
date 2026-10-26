<?php

namespace App\Http\Controllers;

use App\Enums\FacilityType;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Transfer;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display predictive inventory analytics for the current facility scope.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $isRegional = $user->isDinkesAuditor();
        $facilityId = $this->resolveFacilityId($request, $user, $isRegional);
        $batches = $this->scopedBatches($facilityId, $isRegional);
        $completedTransfers = $this->completedTransfers($facilityId, $isRegional);
        $analytics = AnalyticsService::summarize($batches, $completedTransfers);

        return view('analytics.index', [
            'user' => $user,
            'facility' => $facilityId ? Facility::find($facilityId) : null,
            'facilitiesList' => $isRegional
                ? Facility::where('type', '!=', FacilityType::Dinkes->value)->orderBy('name')->get()
                : collect(),
            'isRegional' => $isRegional,
            'analytics' => $analytics,
        ]);
    }

    private function resolveFacilityId(Request $request, User $user, bool $isRegional): ?int
    {
        if (! $isRegional) {
            return $user->facility_id;
        }

        if (! $request->filled('facility_id')) {
            return null;
        }

        $validated = $request->validate([
            'facility_id' => ['nullable', 'integer', 'exists:facilities,id'],
        ]);

        return isset($validated['facility_id']) ? (int) $validated['facility_id'] : null;
    }

    /**
     * @return EloquentCollection<int, Batch>
     */
    private function scopedBatches(?int $facilityId, bool $isRegional): EloquentCollection
    {
        if (! $isRegional && $facilityId === null) {
            return new EloquentCollection;
        }

        return Batch::with(['medicine', 'facility'])
            ->where('status', 'ACTIVE')
            ->when($facilityId !== null, fn ($query) => $query->where('facility_id', $facilityId))
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * @return EloquentCollection<int, Transfer>
     */
    private function completedTransfers(?int $facilityId, bool $isRegional): EloquentCollection
    {
        if (! $isRegional && $facilityId === null) {
            return new EloquentCollection;
        }

        return Transfer::with('batch')
            ->where('status', 'COMPLETED')
            ->when($facilityId !== null, function ($query) use ($facilityId): void {
                $query->where(function ($subQuery) use ($facilityId): void {
                    $subQuery->where('donor_facility_id', $facilityId)
                        ->orWhere('recipient_facility_id', $facilityId);
                });
            })
            ->get();
    }
}
