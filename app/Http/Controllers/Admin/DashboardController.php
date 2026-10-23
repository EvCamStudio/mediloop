<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FacilityRequestStatus;
use App\Enums\FacilityStatus;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityRequest;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'facilities' => Facility::count(),
                'active' => Facility::where('status', FacilityStatus::Active->value)->count(),
                'pendingFacilities' => Facility::where('status', FacilityStatus::Pending->value)->count(),
                'pendingRequests' => FacilityRequest::where('status', FacilityRequestStatus::Pending->value)->count(),
                'users' => User::count(),
            ],
            'pendingRequests' => FacilityRequest::query()
                ->where('status', FacilityRequestStatus::Pending->value)
                ->latest()
                ->limit(5)
                ->get(),
            'recentFacilities' => Facility::query()->latest()->limit(5)->get(),
        ]);
    }
}
