@extends('layouts.mediloop', ['title' => 'Peta Jaringan Faskes'])

@section('content')
<div class="ml-dashboard">

    {{-- Header --}}
    <header class="ml-page-head">
        <div>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Peta Jaringan &amp; Jarak Faskes</h1>
                <span class="ml-facility-chip">{{ $origin?->name ?? 'Faskes belum terhubung' }}</span>
            </div>
            <p class="ml-page-sub">
                Persebaran fasilitas kesehatan mitra dan jarak udara dari faskes Anda, diurutkan dari yang terdekat.
            </p>
        </div>

        <div class="ml-page-actions">
            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                <span>Kelola Semua Stok</span>
            </a>
        </div>
    </header>

    {{-- Summary --}}
    <div class="ml-network-stats">
        <div class="ml-network-stat">
            <span class="ml-network-stat-label">Faskes Terjangkau</span>
            <span class="ml-network-stat-value">{{ $stats['in_range'] }}</span>
            <span class="ml-network-stat-sub">Dalam radius {{ number_format($mapData['maxRadiusKm'], 0, ',', '.') }} km</span>
        </div>
        <div class="ml-network-stat">
            <span class="ml-network-stat-label">Faskes Terdekat</span>
            <span class="ml-network-stat-value">
                {{ $stats['nearest_km'] === null ? '—' : number_format($stats['nearest_km'], 2, ',', '.') }}
                @if ($stats['nearest_km'] !== null)<span class="ml-network-stat-unit">km</span>@endif
            </span>
            <span class="ml-network-stat-sub">Jarak udara (Haversine)</span>
        </div>
        <div class="ml-network-stat">
            <span class="ml-network-stat-label">Faskes Terdaftar</span>
            <span class="ml-network-stat-value">{{ $stats['facilities'] }}</span>
            <span class="ml-network-stat-sub">Setelah filter aktif</span>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('network.index') }}" class="ml-network-toolbar" aria-label="Filter jaringan fasilitas">
        <div class="ml-network-field">
            <label for="network-type">Tipe faskes</label>
            <select id="network-type" name="type">
                <option value="">Semua tipe</option>
                @foreach ($facilityTypes as $type)
                    <option value="{{ $type }}" @selected($selectedType === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <label class="ml-network-check" for="network-cold-chain">
            <input type="checkbox" id="network-cold-chain" name="cold_chain" value="1" @checked($coldChainOnly)>
            <span>Hanya faskes siap rantai dingin</span>
        </label>

        <div class="ml-network-actions">
            <button type="submit" class="ml-btn-primary">Terapkan Filter</button>
            @if ($selectedType !== null || $coldChainOnly)
                <a href="{{ route('network.index') }}" class="ml-btn-quiet">Reset</a>
            @endif
        </div>
    </form>

    <div class="ml-network-layout">
        {{-- Map --}}
        <section class="ml-section" aria-labelledby="network-map-title">
            <div class="ml-section-header">
                <h2 class="ml-section-title" id="network-map-title">Peta Persebaran Faskes</h2>
                <p class="ml-section-sub">
                    @if ($origin)
                        Titik faskes Anda menjadi pusat perhitungan jarak.
                        Lingkaran putus-putus menandai radius {{ number_format($mapData['maxRadiusKm'], 0, ',', '.') }} km.
                    @else
                        Akun ini belum terhubung ke faskes, sehingga jarak dan radius tidak dihitung.
                    @endif
                </p>
            </div>

            <div class="ml-network-map-wrap">
                <div id="network-map" class="ml-network-map" role="region" tabindex="0"
                    aria-label="Peta fasilitas kesehatan. Daftar faskes terdekat tersedia di panel sebelah.">
                    <p class="ml-network-map-fallback">Memuat peta interaktif…</p>
                </div>
                <noscript>
                    <p class="ml-network-noscript">
                        Peta interaktif memerlukan JavaScript. Seluruh informasi jarak dan faskes tetap tersedia pada daftar di bawah.
                    </p>
                </noscript>
            </div>

            <div class="ml-network-legend">
                @if ($origin)
                    <span class="ml-network-legend-item">
                        <span class="ml-network-legend-dot is-origin" aria-hidden="true"></span>
                        Faskes Anda
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
                        'RSUD' => 'RSUD',
                        'PUSKESMAS' => 'Puskesmas',
                        'KLINIK' => 'Klinik',
                        'RS_SWASTA' => 'RS Swasta',
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
                        Radius {{ number_format($mapData['maxRadiusKm'], 0, ',', '.') }} km
                    </span>
                @endif
            </div>

            <p class="ml-network-source">
                Jarak dihitung dengan rumus Haversine (jarak udara), bukan jarak tempuh jalan.
                Koordinat fasilitas merupakan data simulasi untuk demonstrasi.
            </p>
        </section>

        {{-- Nearest list --}}
        <section class="ml-section" aria-labelledby="network-list-title">
            <div class="ml-section-header">
                <h2 class="ml-section-title" id="network-list-title">Terdekat dari Faskes Anda</h2>
                <p class="ml-section-sub">
                    {{ $stats['facilities'] }} faskes ditemukan. Pilih salah satu untuk menyorot lokasinya di peta.
                </p>
            </div>

            <ol class="ml-network-list">
                @forelse ($rows as $index => $row)
                    @php
                        $facility = $row['facility'];
                        $facilityType = match (strtoupper($facility->type)) {
                            'RSUD' => 'RSUD',
                            'PUSKESMAS' => 'Puskesmas',
                            'KLINIK' => 'Klinik',
                            'RS_SWASTA' => 'RS Swasta',
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
                                        Siap rantai dingin
                                    </span>
                                @endif
                            </span>
                            <span class="ml-network-distance">
                                @if ($row['distance_km'] !== null)
                                    {{ number_format($row['distance_km'], 2, ',', '.') }}<span class="ml-network-distance-unit"> km</span>
                                    <span class="ml-network-proximity">Skor jarak {{ number_format((float) $row['proximity_score'], 0) }}%</span>
                                @else
                                    <span class="ml-network-distance-missing">Jarak tidak tersedia</span>
                                @endif
                            </span>
                        </button>
                    </li>
                @empty
                    <li class="ml-network-empty">
                        <p>Belum ada faskes lain yang cocok dengan filter aktif.</p>
                        <a href="{{ route('network.index') }}" class="ml-btn-quiet ml-btn-sm">Tampilkan Semua Faskes</a>
                    </li>
                @endforelse
            </ol>
        </section>
    </div>
</div>

<script type="application/json" id="network-map-data">@json($mapData)</script>
@vite('resources/js/network-map.js')
@endsection
