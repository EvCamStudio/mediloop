@extends('layouts.mediloop', ['title' => __('admin.requests.review_title')])

@section('content')
@php
    $completedChecks = collect($verificationChecklist)->where('complete', true)->count();
    $totalChecks = count($verificationChecklist);
    $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($requestRecord->name, 0, 1));
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('admin.breadcrumb') }}">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin.administration') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('admin.facility-requests.index') }}">{{ __('admin.requests.breadcrumb') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ $requestRecord->name }}</span>
    </nav>

    @if(session('status'))
        <x-alert variant="success">{{ session('status') }}</x-alert>
    @endif

    <x-form.summary for="rejectRequestForm" />

    <header class="ml-passport">
        <div class="ml-passport-main">
            <span class="ml-passport-mark" aria-hidden="true">{{ $initials }}</span>
            <div class="ml-passport-copy">
                <p class="ml-passport-eyebrow">{{ __('admin.facility_request') }} · {{ \App\Enums\FacilityType::tryFrom($requestRecord->type)?->label() ?? $requestRecord->type }}</p>
                <div class="ml-passport-title-row">
                    <h1 class="ml-page-title">{{ $requestRecord->name }}</h1>
                    @include('admin.partials.status', ['status' => $requestRecord->status, 'label' => $requestRecord->status_label])
                </div>
                <p class="ml-passport-meta">
                    <span class="ml-cell-mono">{{ $requestRecord->code }}</span>
                    <span class="ml-meta-dot">·</span>
                    {{ __('admin.requests.submitted_on', ['date' => $requestRecord->created_at->format('d M Y, H:i')]) }}
                    @if($requestRecord->city)
                        <span class="ml-meta-dot">·</span>
                        {{ collect([$requestRecord->city, $requestRecord->province])->filter()->implode(', ') }}
                    @endif
                </p>
            </div>
        </div>
        <div class="ml-passport-actions">
            <a href="{{ route('admin.facility-requests.index') }}" class="ml-btn-quiet">{{ __('admin.requests.back_to_list') }}</a>
        </div>
    </header>

    <div class="ml-two-col">
        <div>
            <section class="ml-card" aria-labelledby="request-data-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="request-data-title">{{ __('admin.requests.data_title') }}</h2>
                        <p class="ml-card-sub">{{ __('admin.requests.data_subtitle') }}</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">01</span>
                </div>
                <div class="ml-card-body">
                    <div class="ml-detail-grid">
                        <div>
                            <div class="ml-detail-label">{{ __('admin.facility_code') }}</div>
                            <div class="ml-detail-value is-mono">{{ $requestRecord->code }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('common.type') }}</div>
                            <div class="ml-detail-value">{{ \App\Enums\FacilityType::tryFrom($requestRecord->type)?->label() ?? $requestRecord->type }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('admin.ownership') }}</div>
                            <div class="ml-detail-value">{{ $requestRecord->ownership ? ucfirst(strtolower($requestRecord->ownership)) : '—' }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('admin.license_number') }}</div>
                            <div class="ml-detail-value">{{ $requestRecord->license_number ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('admin.contact_person') }}</div>
                            <div class="ml-detail-value">{{ $requestRecord->contact_person }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('common.phone') }}</div>
                            <div class="ml-detail-value">{{ $requestRecord->phone }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('common.email') }}</div>
                            <div class="ml-detail-value">{{ $requestRecord->email }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">{{ __('admin.cold_chain') }}</div>
                            <div class="ml-detail-value">
                                <span class="ml-inline-status {{ $requestRecord->cold_chain_ready ? 'is-on' : 'is-off' }}">
                                    {{ $requestRecord->cold_chain_ready ? __('admin.cold_chain_available') : __('common.not_available') }}
                                </span>
                            </div>
                        </div>
                        <div class="is-full">
                            <div class="ml-detail-label">{{ __('common.address') }}</div>
                            <div class="ml-detail-value">
                                {{ $requestRecord->address }}
                                @if($requestRecord->district) · {{ $requestRecord->district }} @endif
                                · {{ collect([$requestRecord->city, $requestRecord->province])->filter()->implode(', ') }}
                            </div>
                        </div>
                        @if($requestRecord->notes)
                            <div class="is-full">
                                <div class="ml-detail-label">{{ __('admin.requests.submitter_notes') }}</div>
                                <div class="ml-detail-value">{{ $requestRecord->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="request-map-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="request-map-title">{{ __('admin.requests.proposed_location_title') }}</h2>
                        <p class="ml-card-sub">{{ __('admin.requests.proposed_location_subtitle') }}</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">02</span>
                </div>
                <div class="ml-card-body">
                    <div id="facility-map" class="ml-map-picker" data-lat="{{ $requestRecord->latitude }}" data-lng="{{ $requestRecord->longitude }}" data-readonly="true" role="application" tabindex="0" aria-label="{{ __('admin.requests.map_aria') }}" aria-describedby="map-coordinates"></div>
                    <div class="ml-map-coordinates">
                        <span>
                            <span class="ml-detail-label">{{ __('admin.coordinates') }}</span>
                            {{ collect([$requestRecord->city, $requestRecord->province])->filter()->implode(', ') ?: __('admin.region_not_filled') }}
                        </span>
                        <code id="map-coordinates">{{ number_format((float) $requestRecord->latitude, 7, '.', '') }}, {{ number_format((float) $requestRecord->longitude, 7, '.', '') }}</code>
                    </div>
                </div>
            </section>
        </div>

        <aside>
            <section class="ml-card ml-verification-card ml-sticky" aria-labelledby="review-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="review-title">{{ __('admin.requests.review_decision') }}</h2>
                    <p class="ml-card-sub">{{ __('admin.verification_documents', ['completed' => $completedChecks, 'total' => $totalChecks]) }}</p>
                </div>
                <div class="ml-card-body">
                    <ul class="ml-checklist">
                        @foreach($verificationChecklist as $item)
                            <li class="ml-checklist-item {{ $item['complete'] ? 'is-complete' : '' }}">
                                <span class="ml-checklist-icon" aria-hidden="true">
                                    @if($item['complete'])
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5" /></svg>
                                    @else
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v5m0 3h.01" /><circle cx="12" cy="12" r="9" /></svg>
                                    @endif
                                </span>
                                <span>{{ $item['label'] }}</span>
                                <span class="ml-sr-only">{{ $item['complete'] ? __('admin.complete') : __('admin.incomplete') }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if($requestRecord->isPending())
                        <div class="ml-review-actions">
                            <form method="POST" action="{{ route('admin.facility-requests.approve', $requestRecord) }}" onsubmit="return confirm('{{ __('admin.requests.approve_confirm') }}');">
                                @csrf
                                <button type="submit" class="ml-btn-primary">{{ __('admin.requests.approve_and_activate') }}</button>
                            </form>

                            <div class="ml-review-reject">
                                <form method="POST" action="{{ route('admin.facility-requests.reject', $requestRecord) }}" id="rejectRequestForm" novalidate data-validate>
                                    @csrf
                                    <div class="ml-field">
                                        <label for="rejection_reason" class="ml-label">{{ __('admin.requests.rejection_reason') }} <span class="ml-required">*</span></label>
                                        <textarea id="rejection_reason" name="rejection_reason" class="ml-textarea @error('rejection_reason') is-invalid @enderror" placeholder="{{ __('admin.requests.rejection_placeholder') }}" required minlength="10" data-error-required="{{ __('admin.validation.rejection_reason_required') }}" data-error-minlength="{{ __('admin.validation.rejection_reason_min') }}">{{ old('rejection_reason') }}</textarea>
                                        <x-form.error for="rejection_reason" />
                                    </div>
                                    <button type="submit" class="ml-btn-danger-quiet">{{ __('admin.requests.reject_request') }}</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="ml-verification-meta">
                            <div>
                                <div class="ml-detail-label">{{ __('admin.requests.decided_at') }}</div>
                                <div class="ml-detail-value">{{ $requestRecord->reviewed_at?->format('d M Y, H:i') ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">{{ __('admin.requests.decided_by') }}</div>
                                <div class="ml-detail-value">{{ $requestRecord->reviewer?->name ?? '—' }}</div>
                            </div>
                            @if($requestRecord->rejection_reason)
                                <div>
                                    <div class="ml-detail-label">{{ __('admin.requests.rejection_reason') }}</div>
                                    <div class="ml-detail-value">{{ $requestRecord->rejection_reason }}</div>
                                </div>
                            @endif
                        </div>
                        @if($requestRecord->facility)
                            <a href="{{ route('admin.facilities.show', $requestRecord->facility) }}" class="ml-btn-quiet ml-review-facility-link">{{ __('admin.requests.open_facility') }}</a>
                        @endif
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/js/facility-map-picker.js')
@endsection
