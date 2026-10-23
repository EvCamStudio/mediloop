<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSysadmin();
    }

    public function create(User $user): bool
    {
        return $user->isSysadmin();
    }

    public function view(User $user, Facility $facility): bool
    {
        if ($user->isDinkesAuditor()) {
            return true;
        }

        return $user->facility_id !== null && $user->facility_id === $facility->id;
    }

    public function update(User $user, Facility $facility): bool
    {
        if ($user->isSysadmin()) {
            return true;
        }

        return $user->isFacilityApprover() && $user->facility_id === $facility->id;
    }

    public function verify(User $user, Facility $facility): bool
    {
        return $user->isSysadmin();
    }

    public function suspend(User $user, Facility $facility): bool
    {
        return $user->isSysadmin();
    }

    public function reactivate(User $user, Facility $facility): bool
    {
        return $user->isSysadmin();
    }

    public function manageTeam(User $user, Facility $facility): bool
    {
        if ($user->isSysadmin()) {
            return true;
        }

        return $user->isFacilityApprover() && $user->facility_id === $facility->id;
    }
}
