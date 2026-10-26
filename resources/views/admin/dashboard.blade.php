@extends('layouts.mediloop', ['title' => __('admin.administration')])

@section('content')
<div class="ml-page-admin">
    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">{{ __('admin.dashboard.heading') }}</h1>
            <p class="ml-page-sub">{{ __('admin.dashboard.subtitle') }}</p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('admin.facilities.index') }}" class="ml-btn-quiet">{{ __('admin.dashboard.open_registry') }}</a>
            <a href="{{ route('admin.facility-requests.index') }}" class="ml-btn-primary">{{ __('admin.dashboard.review_requests') }}</a>
        </div>
    </header>

    <div class="ml-summary-bar" aria-label="{{ __('admin.dashboard.summary_aria') }}">
        <div class="ml-summary-item">
            <span class="ml-summary-value">{{ number_format($counts['active'], 0, ',', '.') }}</span>
            <span class="ml-summary-label">{{ __('admin.dashboard.active_facilities') }}</span>
        </div>
        <div class="ml-summary-item">
            <span class="ml-summary-value">{{ number_format($counts['pendingRequests'], 0, ',', '.') }}</span>
            <span class="ml-summary-label">{{ __('admin.dashboard.pending_requests') }}</span>
        </div>
        <div class="ml-summary-item">
            <span class="ml-summary-value">{{ number_format($counts['pendingFacilities'], 0, ',', '.') }}</span>
            <span class="ml-summary-label">{{ __('admin.dashboard.unverified_facilities') }}</span>
        </div>
        <div class="ml-summary-item">
            <span class="ml-summary-value">{{ number_format($counts['users'], 0, ',', '.') }}</span>
            <span class="ml-summary-label">{{ __('admin.dashboard.user_accounts') }}</span>
        </div>
    </div>

    <div class="ml-two-col">
        <section class="ml-card" aria-labelledby="queue-title">
            <div class="ml-card-head" style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                <div>
                    <h2 class="ml-card-title" id="queue-title">{{ __('admin.dashboard.queue_title') }}</h2>
                    <p class="ml-card-sub">{{ __('admin.dashboard.queue_subtitle') }}</p>
                </div>
                <a href="{{ route('admin.facility-requests.index') }}" class="ml-link-action">{{ __('common.view_all') }}</a>
            </div>

            @if($pendingRequests->isEmpty())
                <div class="ml-empty-state">
                    <p class="ml-empty-title">{{ __('admin.dashboard.queue_empty_title') }}</p>
                    <p class="ml-empty-copy">{{ __('admin.dashboard.queue_empty_copy') }}</p>
                </div>
            @else
                <div class="ml-table-responsive">
                    <table class="ml-table">
                        <caption class="ml-sr-only">{{ __('admin.dashboard.queue_caption') }}</caption>
                        <thead>
                            <tr>
                                <th scope="col">{{ __('admin.facility') }}</th>
                                <th scope="col">{{ __('admin.submitted') }}</th>
                                <th scope="col"><span class="ml-sr-only">{{ __('common.actions') }}</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRequests as $requestRecord)
                                <tr>
                                    <td>
                                        <div class="ml-cell-title">{{ $requestRecord->name }}</div>
                                        <div class="ml-cell-meta">{{ \App\Enums\FacilityType::tryFrom($requestRecord->type)?->label() ?? $requestRecord->type }} · {{ $requestRecord->city }}</div>
                                    </td>
                                    <td class="ml-cell-meta" style="white-space: nowrap;">{{ $requestRecord->created_at->diffForHumans() }}</td>
                                    <td class="is-right">
                                        <a href="{{ route('admin.facility-requests.show', $requestRecord) }}" class="ml-btn-quiet ml-btn-sm">{{ __('admin.review') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="ml-card" aria-labelledby="recent-title">
            <div class="ml-card-head">
                <h2 class="ml-card-title" id="recent-title">{{ __('admin.dashboard.recent_title') }}</h2>
                <p class="ml-card-sub">{{ __('admin.dashboard.recent_subtitle') }}</p>
            </div>
            <div class="ml-card-body">
                @forelse($recentFacilities as $facility)
                    <div style="display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--ml-border);">
                        <div>
                            <a href="{{ route('admin.facilities.show', $facility) }}" class="ml-cell-title" style="text-decoration: none;">{{ $facility->name }}</a>
                            <div class="ml-cell-meta">{{ $facility->type_label }} · {{ $facility->city }}</div>
                        </div>
                        @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
                    </div>
                @empty
                    <p class="ml-help" style="margin-top: 0;">{{ __('admin.dashboard.no_facilities') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
