<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FacilityStatus;
use App\Enums\FacilityType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FacilityPayloadRequest;
use App\Models\Facility;
use App\Models\FacilityInvitation;
use App\Models\FacilityRequest;
use App\Models\User;
use App\Services\FacilityInvitationService;
use App\Services\FacilityOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();
        $type = $request->string('type')->toString();

        $query = Facility::query()->withCount('users')->orderBy('name');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if (in_array($status, array_column(FacilityStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        if (in_array($type, FacilityType::values(), true)) {
            $query->where('type', $type);
        }

        return view('admin.facilities.index', [
            'facilities' => $query->paginate(15)->withQueryString(),
            'search' => $search,
            'status' => $status,
            'type' => $type,
            'counts' => [
                'total' => Facility::count(),
                'active' => Facility::where('status', FacilityStatus::Active->value)->count(),
                'pending' => Facility::where('status', FacilityStatus::Pending->value)->count(),
                'suspended' => Facility::where('status', FacilityStatus::Suspended->value)->count(),
                'inactive' => Facility::where('status', FacilityStatus::Inactive->value)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.facilities.create', [
            'facility' => new Facility([
                'latitude' => -6.1754,
                'longitude' => 106.8272,
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Pusat',
            ]),
        ]);
    }

    public function store(FacilityPayloadRequest $request, FacilityOnboardingService $onboarding): RedirectResponse
    {
        $facility = $onboarding->createFacility($request->validated(), $request->user());

        return redirect()
            ->route('admin.facilities.show', $facility)
            ->with('status', __('admin.messages.created'));
    }

    public function show(Facility $facility): View
    {
        $facility->load(['verifiedBy', 'users' => fn ($query) => $query->orderBy('name')]);

        return view('admin.facilities.show', [
            'facility' => $facility,
            'invitations' => $facility->invitations()
                ->with('inviter')
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->latest()
                ->get(),
            'auditLogs' => $facility->auditLogs()->with('actor')->latest('created_at')->limit(20)->get(),
            'requestRecord' => FacilityRequest::where('facility_id', $facility->id)->latest()->first(),
            'verificationChecklist' => [
                ['label' => __('admin.checklist.license_number'), 'complete' => filled($facility->license_number)],
                ['label' => __('admin.checklist.address'), 'complete' => filled($facility->address)],
                ['label' => __('admin.checklist.coordinates'), 'complete' => filled($facility->latitude) && filled($facility->longitude)],
                ['label' => __('admin.checklist.contact'), 'complete' => filled($facility->contact_person) && filled($facility->phone)],
            ],
        ]);
    }

    public function edit(Facility $facility): View
    {
        return view('admin.facilities.edit', ['facility' => $facility]);
    }

    public function update(
        FacilityPayloadRequest $request,
        Facility $facility,
        FacilityOnboardingService $onboarding,
    ): RedirectResponse {
        $onboarding->updateFacility($facility, $request->validated(), $request->user());

        return redirect()
            ->route('admin.facilities.show', $facility)
            ->with('status', __('admin.messages.updated'));
    }

    public function verify(
        Request $request,
        Facility $facility,
        FacilityOnboardingService $onboarding,
    ): RedirectResponse {
        if ($facility->isActive()) {
            return back()->with('status', __('admin.messages.already_active'));
        }

        $onboarding->verifyFacility($facility, $request->user());

        return back()->with('status', __('admin.messages.verified', ['name' => $facility->name]));
    }

    public function suspend(
        Request $request,
        Facility $facility,
        FacilityOnboardingService $onboarding,
    ): RedirectResponse {
        if ($facility->status === FacilityStatus::Suspended->value) {
            return back()->with('status', __('admin.messages.already_suspended'));
        }

        $onboarding->suspendFacility($facility, $request->user());

        return back()->with('status', __('admin.messages.suspended', ['name' => $facility->name]));
    }

    public function reactivate(
        Request $request,
        Facility $facility,
        FacilityOnboardingService $onboarding,
    ): RedirectResponse {
        $onboarding->reactivateFacility($facility, $request->user());

        return back()->with('status', __('admin.messages.reactivated', ['name' => $facility->name]));
    }

    public function invite(
        Request $request,
        Facility $facility,
        FacilityInvitationService $invitations,
    ): RedirectResponse {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'string', Rule::in(UserRole::facilityValues())],
        ]);

        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $invitations->invite(
            $facility,
            $request->string('email')->trim()->toString(),
            $request->string('role')->toString(),
            $user,
        );

        return back()->with('status', __('admin.messages.invitation_sent'));
    }

    public function revokeInvitation(
        Request $request,
        Facility $facility,
        FacilityInvitation $invitation,
        FacilityInvitationService $invitations,
    ): RedirectResponse {
        abort_unless($invitation->facility_id === $facility->id, 404);

        $invitations->revoke($invitation, $request->user());

        return back()->with('status', __('admin.messages.invitation_revoked'));
    }
}
