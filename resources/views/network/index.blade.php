@extends('layouts.mediloop', ['title' => __('network.page_title')])

@section('content')
<div class="ml-dashboard">

    {{-- Header --}}
    <header class="ml-page-head">
        <div>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">{{ __('network.title') }}</h1>
                <span class="ml-facility-chip">{{ $origin?->name ?? __('network.not_linked') }}</span>
            </div>
            <p class="ml-page-sub">{{ __('network.subtitle') }}</p>
        </div>

        <div class="ml-page-actions">
            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                <span>{{ __('network.manage_stock') }}</span>
            </a>
        </div>
    </header>

    {{-- Summary --}}
    <div class="ml-network-stats">
        <div class="ml-network-stat">
            <span class="ml-network-stat-label">{{ __('network.stats.in_range') }}</span>
            <span class="ml-network-stat-value">{{ $stats['in_range'] }}</span>
            <span class="ml-network-stat-sub">{{ __('network.stats.in_range_sub', ['radius' => number_format($mapData['maxRadiusKm'], 0, ',', '.')]) }}</span>
        </div>
        <div class="ml-network-stat">
            <span class="ml-network-stat-label">{{ __('network.stats.nearest') }}</span>
            <span class="ml-network-stat-value">
                {{ $stats['nearest_km'] === null ? '—' : number_format($stats['nearest_km'], 2, ',', '.') }}
                @if ($stats['nearest_km'] !== null)<span class="ml-network-stat-unit">km</span>@endif
            </span>
            <span class="ml-network-stat-sub">{{ __('network.stats.nearest_sub') }}</span>
        </div>
        <div class="ml-network-stat">
            <span class="ml-network-stat-label">{{ __('network.stats.registered') }}</span>
            <span class="ml-network-stat-value">{{ $stats['facilities'] }}</span>
            <span class="ml-network-stat-sub">{{ __('network.stats.registered_sub') }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('network.index') }}" class="ml-network-toolbar" aria-label="{{ __('network.filters.aria') }}">
        <div class="ml-network-field">
            <label for="network-type">{{ __('network.filters.type_label') }}</label>
            <select id="network-type" name="type">
                <option value="">{{ __('network.filters.all_types') }}</option>
                @foreach ($facilityTypes as $type)
                    <option value="{{ $type }}" @selected($selectedType === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <label class="ml-network-check" for="network-cold-chain">
            <input type="checkbox" id="network-cold-chain" name="cold_chain" value="1" @checked($coldChainOnly)>
            <span>{{ __('network.filters.cold_chain') }}</span>
        </label>

        <div class="ml-network-actions">
            <button type="submit" class="ml-btn-primary">{{ __('network.filters.apply') }}</button>
            @if ($selectedType !== null || $coldChainOnly)
                <a href="{{ route('network.index') }}" class="ml-btn-quiet">{{ __('common.reset') }}</a>
            @endif
        </div>
    </form>

    <div class="ml-network-layout">
        {{-- Map --}}
        <section class="ml-section" aria-labelledby="network-map-title">
            <div class="ml-section-header">
                <h2 class="ml-section-title" id="network-map-title">{{ __('network.map.title') }}</h2>
                <p class="ml-section-sub">
                    @if ($origin)
                        {{ __('network.map.subtitle_origin', ['radius' => number_format($mapData['maxRadiusKm'], 0, ',', '.')]) }}
                    @else
                        {{ __('network.map.subtitle_no_origin') }}
                    @endif
                </p>
            </div>

            <div class="ml-network-map-wrap">
                <div id="network-map" class="ml-network-map" role="region" tabindex="0"
                    aria-label="{{ __('network.map.aria') }}">
                    <p class="ml-network-map-fallback">{{ __('network.map.loading') }}</p>
                </div>
                <noscript>
                    <p class="ml-network-noscript">{{ __('network.map.noscript') }}</p>
                </noscript>
            </div>

            <div class="ml-network-legend">
                @if ($origin)
                    <span class="ml-network-legend-item">
                        <span class="ml-network-legend-dot is-origin" aria-hidden="true"></span>
                        {{ __('network.legend.origin') }}
                    </span>
                @endif
                @php
                    $legendTypeClasses = [
                        'RSUD' => 'is-rsud',
                        'PUSKESMAS' => 'is-puskesmas',
                        'KLINIK' => 'is-klinik',
                        'RS_SWASTA' => 'is-rs-swasta',
                    ];
                    $legendTypeLabels = [
                        'RSUD' => __('enums.facility_type.rsud'),
                        'PUSKESMAS' => __('enums.facility_type.puskesmas'),
                        'KLINIK' => __('enums.facility_type.klinik'),
                        'RS_SWASTA' => __('enums.facility_type.rs_swasta'),
                    ];
                @endphp
                @foreach ($facilityTypes as $type)
                    <span class="ml-network-legend-item">
                        <span class="ml-network-legend-dot {{ $legendTypeClasses[$type] ?? 'is-other' }}"
                            aria-hidden="true"></span>
                        {{ $legendTypeLabels[$type] ?? $type }}
                    </span>
                @endforeach
                @if ($origin)
                    <span class="ml-network-legend-item">
                        <span class="ml-network-legend-dot is-radius" aria-hidden="true"></span>
                        {{ __('network.legend.radius', ['radius' => number_format($mapData['maxRadiusKm'], 0, ',', '.')]) }}
                    </span>
                @endif
            </div>

            <p class="ml-network-source">{{ __('network.map.source') }}</p>
        </section>

        {{-- Nearest list --}}
        <section class="ml-section" aria-labelledby="network-list-title">
            <div class="ml-section-header">
                <h2 class="ml-section-title" id="network-list-title">{{ __('network.list.title') }}</h2>
                <p class="ml-section-sub">
                    {{ trans_choice('network.list.found', $stats['facilities'], ['count' => $stats['facilities']]) }}
                </p>
            </div>

            <ol class="ml-network-list">
                @forelse ($rows as $index => $row)
                    @php
                        $facility = $row['facility'];
                        $facilityType = match (strtoupper($facility->type)) {
                            'RSUD' => __('enums.facility_type.rsud'),
                            'PUSKESMAS' => __('enums.facility_type.puskesmas'),
                            'KLINIK' => __('enums.facility_type.klinik'),
                            'RS_SWASTA' => __('enums.facility_type.rs_swasta'),
                            default => ucwords(strtolower(str_replace('_', ' ', $facility->type))),
                        };
                    @endphp
                    <li class="ml-network-item">
                        <button type="button" class="ml-network-target" data-facility-id="{{ $facility->id }}">
                            <span class="ml-network-rank" aria-hidden="true">{{ $index + 1 }}</span>
                            <span class="ml-network-body">
                                <span class="ml-network-name">{{ $facility->name }}</span>
                                <span class="ml-network-meta">{{ $facilityType }} &middot; {{ $facility->city }}</span>
                                <span class="ml-network-meta">{{ $facility->address }}</span>
                                @if ($facility->cold_chain_ready)
                                    <span class="ml-network-cold">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9L4.9 19.1"/></svg>
                                        {{ __('network.list.cold_chain') }}
                                    </span>
                                @endif
                            </span>
                            <span class="ml-network-distance">
                                @if ($row['distance_km'] !== null)
                                    {{ number_format($row['distance_km'], 2, ',', '.') }}<span class="ml-network-distance-unit"> km</span>
                                    <span class="ml-network-proximity">{{ __('network.list.proximity', ['score' => number_format((float) $row['proximity_score'], 0)]) }}</span>
                                @else
                                    <span class="ml-network-distance-missing">{{ __('network.list.distance_missing') }}</span>
                                @endif
                            </span>
                        </button>
                    </li>
                @empty
                    <li class="ml-network-empty">
                        <p>{{ __('network.list.empty') }}</p>
                        <a href="{{ route('network.index') }}" class="ml-btn-quiet ml-btn-sm">{{ __('network.list.show_all') }}</a>
                    </li>
                @endforelse
            </ol>
        </section>
    </div>
</div>

<script type="application/json" id="network-map-data">@json($mapData)</script>
@vite('resources/js/network-map.js')
@endsection
