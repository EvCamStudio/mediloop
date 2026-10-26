@extends('layouts.mediloop', ['title' => __('admin.requests.title')])

@section('content')
@php
    $tabs = [
        ['label' => __('common.all'), 'value' => '', 'count' => $counts['total']],
        ['label' => __('admin.status.pending'), 'value' => 'PENDING', 'count' => $counts['pending']],
        ['label' => __('admin.status.approved'), 'value' => 'APPROVED', 'count' => $counts['approved']],
        ['label' => __('admin.status.rejected'), 'value' => 'REJECTED', 'count' => $counts['rejected']],
    ];
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('admin.breadcrumb') }}">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin.administration') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('admin.facility_request') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">{{ __('admin.requests.heading') }}</h1>
            <p class="ml-page-sub">{{ __('admin.requests.subtitle') }}</p>
        </div>
    </header>

    @if(session('status'))
        <div class="ml-alert ml-alert-success" role="status">{{ session('status') }}</div>
    @endif

    <nav class="ml-registry-tabs" aria-label="{{ __('admin.requests.filter_status_aria') }}">
        @foreach($tabs as $tab)
            <a
                href="{{ $tab['value'] === '' ? route('admin.facility-requests.index') : route('admin.facility-requests.index', ['status' => $tab['value']]) }}"
                class="ml-registry-tab {{ $status === $tab['value'] ? 'is-active' : '' }}"
                @if($status === $tab['value']) aria-current="page" @endif
            >
                {{ $tab['label'] }}
                <span class="ml-tab-count">{{ number_format($tab['count'], 0, ',', '.') }}</span>
            </a>
        @endforeach
    </nav>

    <section class="ml-registry-panel" aria-labelledby="requests-table-title">
        <h2 id="requests-table-title" class="ml-sr-only">{{ __('admin.requests.list_aria') }}</h2>

        @if($requests->isEmpty())
            <div class="ml-empty-state">
                <p class="ml-empty-title">{{ __('admin.requests.empty_title') }}</p>
                <p class="ml-empty-copy">{{ __('admin.requests.empty_copy') }}</p>
                <a href="{{ route('admin.facility-requests.index') }}" class="ml-btn-quiet">{{ __('common.view_all') }}</a>
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy ml-table-registry ml-table-requests">
                    <caption class="ml-sr-only">{{ __('admin.requests.table_caption') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('admin.facility') }}</th>
                            <th scope="col">{{ __('admin.location') }}</th>
                            <th scope="col">{{ __('admin.submitted') }}</th>
                            <th scope="col">{{ __('common.status') }}</th>
                            <th scope="col"><span class="ml-sr-only">{{ __('common.actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $requestRecord)
                            <tr>
                                <td>
                                    <div class="ml-facility-cell">
                                        <span class="ml-facility-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($requestRecord->name, 0, 1)) }}</span>
                                        <span>
                                            <a href="{{ route('admin.facility-requests.show', $requestRecord) }}" class="ml-facility-name">{{ $requestRecord->name }}</a>
                                            <span class="ml-cell-meta">
                                                <span class="ml-cell-mono">{{ $requestRecord->code }}</span>
                                                <span class="ml-meta-dot">·</span>
                                                {{ \App\Enums\FacilityType::tryFrom($requestRecord->type)?->label() ?? $requestRecord->type }}
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td data-label="{{ __('admin.location') }}">
                                    <div>{{ $requestRecord->city ?: '—' }}</div>
                                    <div class="ml-cell-meta">{{ $requestRecord->province ?: __('admin.region_not_filled') }}</div>
                                </td>
                                <td data-label="{{ __('admin.submitted') }}" class="ml-cell-meta">
                                    {{ $requestRecord->created_at->format('d M Y') }}
                                </td>
                                <td data-label="{{ __('common.status') }}">
                                    @include('admin.partials.status', ['status' => $requestRecord->status, 'label' => $requestRecord->status_label])
                                </td>
                                <td class="is-right">
                                    <a href="{{ route('admin.facility-requests.show', $requestRecord) }}" class="ml-row-open">
                                        {{ __('admin.review') }}
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M5 12h14M13 6l6 6-6 6" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="ml-pagination-wrap">
                {{ $requests->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
