@extends('layouts.mediloop', ['title' => __('transfers.propose.page_title')])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('transfers.breadcrumb_aria') }}">
        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('transfers.index') }}">{{ __('transfers.breadcrumb') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('transfers.propose.breadcrumb', ['batch' => $batch->batch_number]) }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">{{ __('transfers.propose.eyebrow') }}</p>
            <h1 class="ml-page-title">{{ __('transfers.propose.title', ['medicine' => $batch->medicine->name]) }}</h1>
            <p class="ml-page-sub">{{ __('transfers.propose.subtitle') }}</p>
        </div>
    </header>

    <div class="ml-alert ml-alert-info ml-compliance-banner" role="note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
            <path d="M12 8v4m0 4h.01" />
        </svg>
        <div>
            <strong>{{ __('transfers.compliance.title') }}</strong>
            <p>{{ __('transfers.compliance.proposal') }}</p>
        </div>
    </div>

    <div class="ml-two-col">
        <div>
            <section class="ml-card" aria-labelledby="source-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="source-title">{{ __('transfers.propose.source.title') }}</h2>
                        <p class="ml-card-sub">{{ __('transfers.propose.source.meta', ['facility' => $batch->facility->name, 'batch' => $batch->batch_number]) }}</p>
                    </div>
                    <span class="ml-risk ml-risk-high">{{ __('transfers.risk.high') }}</span>
                </div>
                <div class="ml-card-body">
                    <div class="ml-metric-grid">
                        <div class="ml-metric">
                            <span class="ml-metric-label">{{ __('transfers.propose.metrics.donor_stock') }}</span>
                            <span class="ml-metric-value">{{ number_format($batch->current_stock) }} <small>{{ $batch->medicine->unit }}</small></span>
                        </div>
                        <div class="ml-metric">
                            <span class="ml-metric-label">{{ __('transfers.propose.metrics.burn_rate') }}</span>
                            <span class="ml-metric-value">{{ $batch->daily_burn_rate }} <small>{{ __('transfers.units.per_day') }}</small></span>
                        </div>
                        <div class="ml-metric">
                            <span class="ml-metric-label">{{ __('transfers.propose.metrics.remaining_expiry') }}</span>
                            <span class="ml-metric-value">{{ $batch->days_to_expiry }} <small>{{ __('common.days') }}</small></span>
                        </div>
                        <div class="ml-metric is-risk">
                            <span class="ml-metric-label">{{ __('transfers.propose.metrics.projected_waste') }}</span>
                            <span class="ml-metric-value">{{ number_format($batch->projected_waste) }} <small>{{ __('common.units') }}</small></span>
                        </div>
                    </div>
                    <p class="ml-help">{{ __('transfers.propose.source.help') }}</p>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="candidates-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="candidates-title">{{ __('transfers.propose.candidates.title') }}</h2>
                        <p class="ml-card-sub">{{ __('transfers.propose.candidates.weights') }}</p>
                    </div>
                    <span class="ml-tab-count">{{ trans_choice('transfers.propose.candidates.count', count($candidates), ['count' => count($candidates)]) }}</span>
                </div>

                <div class="ml-sort-row" role="group" aria-label="{{ __('transfers.propose.candidates.sort_aria') }}">
                    <a
                        href="{{ route('transfers.propose', ['batch' => $batch, 'sort' => 'score']) }}"
                        class="ml-sort-pill {{ $sort === 'score' ? 'is-active' : '' }}"
                        @if($sort === 'score') aria-current="true" @endif
                    >{{ __('transfers.propose.candidates.sort_best') }}</a>
                    <a
                        href="{{ route('transfers.propose', ['batch' => $batch, 'sort' => 'distance']) }}"
                        class="ml-sort-pill {{ $sort === 'distance' ? 'is-active' : '' }}"
                        @if($sort === 'distance') aria-current="true" @endif
                    >{{ __('transfers.propose.candidates.sort_nearest') }}</a>
                    <span class="ml-sort-note">{{ __('transfers.propose.candidates.sort_note') }}</span>
                </div>

                @if(count($candidates) === 0)
                    <div class="ml-empty-state">
                        <p class="ml-empty-title">{{ __('transfers.propose.candidates.empty_title') }}</p>
                        <p class="ml-empty-copy">{{ __('transfers.propose.candidates.empty_copy') }}</p>
                    </div>
                @else
                    <ol class="ml-candidate-list">
                        @foreach($candidates as $index => $candidate)
                            <li class="ml-candidate {{ $index === 0 ? 'is-top' : '' }}">
                                <div class="ml-candidate-rank" aria-hidden="true">{{ $index + 1 }}</div>
                                <div class="ml-candidate-copy">
                                    <div class="ml-candidate-name">{{ $candidate['facility']->name }}</div>
                                    <div class="ml-cell-meta">
                                        {{ \App\Enums\FacilityType::tryFrom($candidate['facility']->type)?->label() ?? $candidate['facility']->type }}
                                        <span class="ml-meta-dot">·</span>
                                        {{ $candidate['facility']->city ?? $candidate['facility']->address }}
                                    </div>
                                    <div class="ml-candidate-metrics">
                                        <span>{{ __('transfers.propose.candidates.need') }} <strong>{{ $candidate['need_score'] }}%</strong></span>
                                        <span>{{ __('transfers.propose.candidates.distance') }} <strong>{{ $candidate['proximity_score'] }}%</strong></span>
                                        <span>{{ __('transfers.propose.candidates.logistics') }} <strong>{{ $candidate['logistics_score'] }}%</strong></span>
                                        <span>{{ __('transfers.propose.candidates.capacity') }} <strong>{{ $candidate['recipient_intake_cap'] }} {{ __('common.units') }}</strong></span>
                                    </div>
                                </div>
                                <div class="ml-candidate-side">
                                    <div class="ml-candidate-score">{{ $candidate['match_score'] }}%</div>
                                    <div class="ml-cell-meta">{{ $candidate['distance_km'] }} km</div>
                                    <button
                                        type="button"
                                        class="ml-btn-primary ml-btn-sm"
                                        onclick="selectCandidate({{ $candidate['facility']->id }}, '{{ addslashes($candidate['facility']->name) }}', {{ $candidate['recommended_quantity'] }})"
                                    >{{ __('transfers.propose.candidates.select') }}</button>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        </div>

        <aside>
            <form method="POST" action="{{ route('transfers.store') }}" class="ml-card ml-sticky" id="proposalForm" novalidate data-validate>
                @csrf
                <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                <input type="hidden" id="selectedFacilityId" name="recipient_facility_id" value="{{ $candidates[0]['facility']->id ?? '' }}">

                <div class="ml-card-head">
                    <h2 class="ml-card-title">{{ __('transfers.propose.form.title') }}</h2>
                    <p class="ml-card-sub">{{ __('transfers.propose.form.subtitle') }}</p>
                </div>
                <div class="ml-card-body">
                    <x-form.summary />
                    <x-form.error for="batch_id" />

                    <div class="ml-field">
                        <label for="selectedFacilityName" class="ml-label">{{ __('transfers.propose.form.recipient_facility') }}</label>
                        <input type="text" id="selectedFacilityName" readonly value="{{ $candidates[0]['facility']->name ?? '' }}" class="ml-input" @if($errors->has('recipient_facility_id')) aria-invalid="true" aria-describedby="recipient_facility_id-error" @endif>
                        <x-form.error for="recipient_facility_id" />
                    </div>

                    <div class="ml-field">
                        <label for="quantity" class="ml-label">{{ __('transfers.propose.form.quantity') }} <span class="ml-required">*</span></label>
                        <input type="number" id="quantity" name="quantity" min="1" max="{{ $batch->current_stock }}" value="{{ $candidates[0]['recommended_quantity'] ?? 100 }}" class="ml-input is-mono @error('quantity') is-invalid @enderror" required data-error-required="{{ __('transfers.propose.form.quantity_required') }}" data-error-min="{{ __('transfers.propose.form.quantity_min') }}" data-error-max="{{ __('transfers.propose.form.quantity_max', ['max' => number_format($batch->current_stock)]) }}">
                        <p class="ml-help">{{ __('transfers.propose.form.quantity_help', ['max' => number_format($batch->current_stock), 'unit' => $batch->medicine->unit]) }}</p>
                        <x-form.error for="quantity" />
                    </div>

                    <div class="ml-field">
                        <label for="notes" class="ml-label">{{ __('transfers.propose.form.notes') }}</label>
                        <textarea id="notes" name="notes" class="ml-textarea @error('notes') is-invalid @enderror" rows="4" maxlength="500" data-error-maxlength="{{ __('transfers.propose.form.notes_maxlength') }}" placeholder="{{ __('transfers.propose.form.notes_placeholder') }}">{{ __('transfers.propose.form.notes_default', ['medicine' => $batch->medicine->name]) }}</textarea>
                        <x-form.error for="notes" />
                    </div>

                    <div class="ml-action-row">
                        <span class="ml-help">{{ __('transfers.propose.form.proposer') }} <strong>{{ $user->name }}</strong></span>
                        <button type="submit" class="ml-btn-primary">
                            {{ __('transfers.propose.form.submit') }}
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </div>
            </form>
        </aside>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectCandidate(facilityId, facilityName, recommendedQty) {
        document.getElementById('selectedFacilityId').value = facilityId;
        document.getElementById('selectedFacilityName').value = facilityName;
        document.getElementById('quantity').value = recommendedQty;
        document.getElementById('proposalForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.getElementById('quantity').focus({ preventScroll: true });
    }
</script>
@endsection
