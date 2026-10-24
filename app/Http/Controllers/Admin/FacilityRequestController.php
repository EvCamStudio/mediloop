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
                ['label' => 'Nomor izin fasilitas', 'complete' => filled($facilityRequest->license_number)],
                ['label' => 'Alamat lengkap', 'complete' => filled($facilityRequest->address)],
                ['label' => 'Titik koordinat', 'complete' => filled($facilityRequest->latitude) && filled($facilityRequest->longitude)],
                ['label' => 'Kontak penanggung jawab', 'complete' => filled($facilityRequest->contact_person) && filled($facilityRequest->phone)],
            ],
        ]);
    }

    public function approve(
        Request $request,
        FacilityRequest $facilityRequest,
        FacilityOnboardingService $onboarding,
    ): RedirectResponse {
        if (! $facilityRequest->isPending()) {
            return back()->withErrors(['request' => 'Pengajuan ini sudah direview.']);
        }

        $facility = $onboarding->approveRequest($facilityRequest, $request->user());

        return redirect()
            ->route('admin.facilities.show', $facility)
            ->with('status', 'Pengajuan disetujui. Fasilitas aktif dan siap diundang anggotanya.');
    }

    public function reject(
        Request $request,
        FacilityRequest $facilityRequest,
        FacilityOnboardingService $onboarding,
    ): RedirectResponse {
        if (! $facilityRequest->isPending()) {
            return back()->withErrors(['request' => 'Pengajuan ini sudah direview.']);
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi agar pengaju dapat memperbaiki data.',
            'rejection_reason.min' => 'Tulis alasan yang cukup jelas (minimal 10 karakter).',
        ]);

        $onboarding->rejectRequest($facilityRequest, $request->user(), $validated['rejection_reason']);

        return redirect()
            ->route('admin.facility-requests.index')
            ->with('status', 'Pengajuan ditolak dengan catatan.');
    }
}
