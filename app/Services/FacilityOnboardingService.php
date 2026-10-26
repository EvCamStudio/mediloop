<?php

namespace App\Services;

use App\Enums\FacilityRequestStatus;
use App\Enums\FacilityStatus;
use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\FacilityRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FacilityOnboardingService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createFacility(array $data, User $actor): Facility
    {
        $facility = Facility::create([
            ...Arr::only($data, [
                'name', 'code', 'type', 'ownership', 'license_number', 'address',
                'city', 'province', 'district', 'latitude', 'longitude',
                'cold_chain_ready', 'contact_person', 'phone', 'email', 'notes',
            ]),
            'status' => FacilityStatus::Pending->value,
        ]);

        $this->log($actor, 'FACILITY_CREATED', __('facility.audit.created', ['facility' => $facility->name]), $facility);

        return $facility;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateFacility(Facility $facility, array $data, User $actor): void
    {
        $facility->update(Arr::only($data, [
            'name', 'code', 'type', 'ownership', 'license_number', 'address',
            'city', 'province', 'district', 'latitude', 'longitude',
            'cold_chain_ready', 'contact_person', 'phone', 'email', 'notes',
        ]));

        $this->log($actor, 'FACILITY_UPDATED', __('facility.audit.updated', ['facility' => $facility->name]), $facility);
    }

    public function approveRequest(FacilityRequest $request, User $reviewer): Facility
    {
        return DB::transaction(function () use ($request, $reviewer): Facility {
            $facility = Facility::create([
                ...Arr::only($request->getAttributes(), [
                    'name', 'code', 'type', 'ownership', 'license_number', 'address',
                    'city', 'province', 'district', 'latitude', 'longitude',
                    'cold_chain_ready', 'contact_person', 'phone', 'email', 'notes',
                ]),
                'status' => FacilityStatus::Active->value,
                'verified_at' => now(),
                'verified_by' => $reviewer->id,
            ]);

            $request->update([
                'status' => FacilityRequestStatus::Approved->value,
                'facility_id' => $facility->id,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            $this->log($reviewer, 'FACILITY_APPROVED', __('facility.audit.request_approved', ['facility' => $request->name]), $facility);

            return $facility;
        });
    }

    public function rejectRequest(FacilityRequest $request, User $reviewer, string $reason): void
    {
        $request->update([
            'status' => FacilityRequestStatus::Rejected->value,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $this->log($reviewer, 'FACILITY_REJECTED', __('facility.audit.request_rejected', ['facility' => $request->name, 'reason' => $reason]));
    }

    public function verifyFacility(Facility $facility, User $reviewer): void
    {
        $facility->update([
            'status' => FacilityStatus::Active->value,
            'verified_at' => now(),
            'verified_by' => $reviewer->id,
        ]);

        $this->log($reviewer, 'FACILITY_VERIFIED', __('facility.audit.verified', ['facility' => $facility->name]), $facility);
    }

    public function suspendFacility(Facility $facility, User $actor): void
    {
        $facility->update(['status' => FacilityStatus::Suspended->value]);

        $this->log($actor, 'FACILITY_SUSPENDED', __('facility.audit.suspended', ['facility' => $facility->name]), $facility);
    }

    public function reactivateFacility(Facility $facility, User $actor): void
    {
        $facility->update([
            'status' => FacilityStatus::Active->value,
            'verified_at' => $facility->verified_at ?? now(),
            'verified_by' => $facility->verified_by ?? $actor->id,
        ]);

        $this->log($actor, 'FACILITY_REACTIVATED', __('facility.audit.reactivated', ['facility' => $facility->name]), $facility);
    }

    /**
     * @param  array<string, mixed>  $delta
     */
    private function log(User $actor, string $action, string $description, ?Facility $facility = null, array $delta = []): void
    {
        AuditLog::create([
            'actor_id' => $actor->id,
            'facility_id' => $facility?->id,
            'action' => $action,
            'description' => $description,
            'state_delta' => $delta,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
