<?php

namespace App\Policies;

use App\Models\FacilityRequest;
use App\Models\User;

class FacilityRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSysadmin();
    }

    public function view(User $user, FacilityRequest $facilityRequest): bool
    {
        return $user->isSysadmin();
    }

    public function approve(User $user, FacilityRequest $facilityRequest): bool
    {
        return $user->isSysadmin() && $facilityRequest->isPending();
    }

    public function reject(User $user, FacilityRequest $facilityRequest): bool
    {
        return $user->isSysadmin() && $facilityRequest->isPending();
    }
}
