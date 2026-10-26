@extends('layouts.mediloop', ['title' => __('transfers.index.page_title')])

@section('content')
@php
    $tabs = [
        ['label' => __('common.all'), 'value' => ''],
        ['label' => __('transfers.tabs.awaiting_donor'), 'value' => 'PROPOSED'],
        ['label' => __('transfers.tabs.awaiting_recipient'), 'value' => 'APPROVED'],
        ['label' => __('transfers.tabs.in_transit'), 'value' => 'IN_TRANSIT'],
        ['label' => __('transfers.tabs.completed'), 'value' => 'COMPLETED'],
    ];

    $statusLabels = [
        'PROPOSED' => ['label' => __('transfers.tabs.awaiting_donor'), 'tone' => 'pending'],
        'APPROVED' => ['label' => __('transfers.tabs.awaiting_recipient'), 'tone' => 'active'],
        'ACCEPTED' => ['label' => __('transfers.tabs.ready_to_ship'), 'tone' => 'active'],
        'IN_TRANSIT' => ['label' => __('transfers.tabs.in_transit'), 'tone' => 'transit'],
        'COMPLETED' => ['label' => __('transfers.tabs.completed'), 'tone' => 'inactive'],
        'CANCELLED' => ['label' => __('transfers.tabs.cancelled'), 'tone' => 'suspended'],
    ];
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('transfers.breadcrumb_aria') }}">
        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('transfers.breadcrumb') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">{{ __('transfers.index.eyebrow') }}</p>
            <h1 class="ml-page-title">{{ __('transfers.index.title') }}</h1>
            <p class="ml-page-sub">{{ __('transfers.index.subtitle') }}</p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('dashboard') }}" class="ml-btn-quiet">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                {{ __('transfers.index.propose_from_stock') }}
            </a>
        </div>
    </header>

    <nav class="ml-registry-tabs" aria-label="{{ __('transfers.index.tabs_aria') }}">
        @foreach($tabs as $tab)
            <a
                href="{{ $tab['value'] === '' ? route('transfers.index') : route('transfers.index', ['status' => $tab['value']]) }}"
                class="ml-registry-tab {{ $currentStatus === $tab['value'] ? 'is-active' : '' }}"
                @if($currentStatus === $tab['value']) aria-current="page" @endif
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>

    <section class="ml-registry-panel" aria-labelledby="transfers-title">
        <h2 id="transfers-title" class="ml-sr-only">{{ __('transfers.index.list_title') }}</h2>

        @if($transfers->isEmpty())
            <div class="ml-empty-state">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path d="M4 7h11l4 4v6H4z" />
                    <path d="M4 7V5h8v2M8 13h6" />
                </svg>
                <p class="ml-empty-title">{{ __('transfers.index.empty_title') }}</p>
                <p class="ml-empty-copy">{{ __('transfers.index.empty_copy') }}</p>
                <a href="{{ route('dashboard') }}" class="ml-btn-quiet">{{ __('transfers.index.empty_cta') }}</a>
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy ml-table-registry ml-table-transfers">
                    <caption class="ml-sr-only">{{ __('transfers.index.table_caption') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('transfers.table.transfer') }}</th>
                            <th scope="col">{{ __('transfers.table.medicine_quantity') }}</th>
                            <th scope="col">{{ __('transfers.table.facility_route') }}</th>
                            <th scope="col">{{ __('transfers.table.score') }}</th>
                            <th scope="col">{{ __('common.status') }}</th>
                            <th scope="col"><span class="ml-sr-only">{{ __('common.actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            @php
                                $statusMeta = $statusLabels[$transfer->status] ?? ['label' => $transfer->status, 'tone' => 'inactive'];
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('transfers.show', $transfer) }}" class="ml-transfer-code">{{ $transfer->transfer_code }}</a>
                                    <div class="ml-cell-meta">{{ $transfer->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td data-label="{{ __('transfers.table.medicine_quantity') }}">
                                    <div class="ml-cell-title">{{ $transfer->batch->medicine->name }}</div>
                                    <div class="ml-cell-meta">
                                        {{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}
                                        <span class="ml-meta-dot">·</span>
                                        {{ __('transfers.batch_label', ['batch' => $transfer->batch->batch_number]) }}
                                    </div>
                                </td>
                                <td data-label="{{ __('transfers.table.facility_route') }}">
                                    <div class="ml-route">
                                        <span class="ml-route-name">{{ $transfer->donorFacility->name }}</span>
                                        <span class="ml-route-arrow" aria-hidden="true">→</span>
                                        <span class="ml-route-name is-recipient">{{ $transfer->recipientFacility->name }}</span>
                                    </div>
                                    <div class="ml-cell-meta">{{ $transfer->distance_km }} km · {{ __('transfers.index.air_distance') }}</div>
                                </td>
                                <td data-label="{{ __('transfers.table.score') }}">
                                    <span class="ml-score">{{ $transfer->match_score }}%</span>
                                </td>
                                <td data-label="{{ __('common.status') }}">
                                    <span class="ml-status ml-status-{{ $statusMeta['tone'] }}">{{ $statusMeta['label'] }}</span>
                                </td>
                                <td class="is-right">
                                    <a href="{{ route('transfers.show', $transfer) }}" class="ml-row-open">
                                        {{ __('transfers.index.track') }}
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
                {{ $transfers->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
