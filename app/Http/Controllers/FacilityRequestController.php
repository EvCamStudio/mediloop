<?php

namespace App\Http\Controllers;

use App\Enums\FacilityRequestStatus;
use App\Http\Requests\PublicFacilityRequest;
use App\Models\FacilityRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FacilityRequestController extends Controller
{
    public function create(): View
    {
        return view('facility-requests.create', [
            'submitted' => session()->pull('facility_request_submitted', false),
        ]);
    }

    public function store(PublicFacilityRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = Str::upper($data['code']);
        $data['status'] = FacilityRequestStatus::Pending->value;

        FacilityRequest::create($data);

        return redirect()
            ->route('facility-requests.create')
            ->with('facility_request_submitted', true);
    }
}
