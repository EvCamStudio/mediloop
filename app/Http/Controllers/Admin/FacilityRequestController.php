<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FacilityRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\FacilityRequest;
use App\Services\FacilityOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $query = FacilityRequest::query()->with('reviewer')->latest();

        if (in_array($status, array_column(FacilityRequestStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        $requests = $query
            ->orderByRaw("CASE WHEN status = 'PENDING' THEN 0 ELSE 1 END")
            ->paginate(15)
            ->withQueryString();

        return view('admin.facility-requests.index', [
            'requests' => $requests,
            'status' => $status,
            'counts' => [
                'total' => FacilityRequest::count(),
                'pending' => FacilityRequest::where('status', FacilityRequestStatus::Pending->value)->count(),
                'approved' => FacilityRequest::where('status', FacilityRequestStatus::Approved->value)->count(),
                'rejected' => FacilityRequest::where('status', FacilityRequestStatus::Rejected->value)->count(),
            ],
        ]);
    }

    public function show(FacilityRequest $facilityRequest): View
    {
        $facilityRequest->load(['reviewer', 'facility']);

        return view('admin.facility-requests.show', [
            'requestRecord' => $facilityRequest,
            'verificationChecklist' => [
                ['label' => __('admin.checklist.license_number'), 'complete' => filled($facilityRequest->license_number)],
                ['label' => __('admin.checklist.address'), 'complete' => filled($facilityRequest->address)],
                ['label' => __('admin.checklist.coordinates'), 'complete' => filled($facilityRequest->latitude) && filled($facilityRequest->longitude)],
                ['label' => __('admin.checklist.contact'), 'complete' => filled($facilityRequest->contact_person) && filled($facilityRequest->phone)],
            ],
        ]);
    }

    public function approve(
        Request $request,
        FacilityRequest $facilityRequest,
        FacilityOnboardingService $onboarding,
    ): RedirectResponse {
        if (! $facilityRequest->isPending()) {
            return back()->withErrors(['request' => __('admin.errors.already_reviewed')]);
        }

        $facility = $onboarding->approveRequest($facilityRequest, $request->user());

        return redirect()
            ->route('admin.facilities.show', $facility)
            ->with('status', __('admin.messages.approved'));
    }

    public function reject(
        Request $request,
        FacilityRequest $facilityRequest,
        FacilityOnboardingService $onboarding,
    ): RedirectResponse {
        if (! $facilityRequest->isPending()) {
            return back()->withErrors(['request' => __('admin.errors.already_reviewed')]);
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rejection_reason.required' => __('admin.validation.rejection_reason_required_for_submitter'),
            'rejection_reason.min' => __('admin.validation.rejection_reason_min'),
        ]);

        $onboarding->rejectRequest($facilityRequest, $request->user(), $validated['rejection_reason']);

        return redirect()
            ->route('admin.facility-requests.index')
            ->with('status', __('admin.messages.rejected'));
    }
}
