@extends('layouts.mediloop', ['title' => 'Stok & Batch Obat'])

@section('content')
@php
    $riskLabels = [
        'CRITICAL' => 'Kritis',
        'HIGH' => 'Tinggi',
        'MEDIUM' => 'Sedang',
        'LOW' => 'Rendah',
    ];

    $filterParams = array_filter([
        'search' => $currentSearch !== '' ? $currentSearch : null,
        'facility_id' => $user->isDinkesAuditor() ? request('facility_id') : null,
        'tab' => ($currentTab && $currentTab !== 'all') ? $currentTab : null,
        'sort' => ($currentSort && $currentSort !== 'expiry_asc') ? $currentSort : null,
        'status' => $currentStatus !== '' ? $currentStatus : null,
    ], fn ($value) => $value !== null && $value !== '');

    $hasFilters = $currentSearch !== ''
        || $currentStatus !== ''
        || ($currentTab && $currentTab !== 'all')
        || ($currentSort && $currentSort !== 'expiry_asc')
        || ($user->isDinkesAuditor() && request()->filled('facility_id'));
@endphp

<div class="ml-page-admin">
    @if(session('status'))
        <div class="ml-alert ml-alert-success" role="status">
            <div class="ml-alert-row">
                <svg class="ml-alert-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
                <button type="button" class="ml-alert-close" onclick="this.closest('.ml-alert').remove()" aria-label="Tutup notifikasi">&times;</button>
            </div>
        </div>
    @endif

    @if($errors->has('delete_error'))
        <div class="ml-alert ml-alert-error" role="alert">
            <div class="ml-alert-row">
                <svg class="ml-alert-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ $errors->first('delete_error') }}</span>
            </div>
        </div>
    @endif

    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Stok &amp; batch</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">Inventaris farmasi</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Stok &amp; batch obat</h1>
                <span class="ml-facility-chip">{{ $facility?->name ?? 'Fasilitas kesehatan' }}</span>
            </div>
            <p class="ml-page-sub">Pantau sisa umur batch, proyeksi limbah, dan kandidat redistribusi sebelum obat kedaluwarsa.</p>
        </div>

        @if(! $user->isDinkesAuditor())
            <div class="ml-page-actions flex items-center gap-2">
                <button type="button" onclick="document.getElementById('ml-csv-modal').classList.remove('hidden')" class="ml-btn-quiet inline-flex items-center gap-1.5">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span>Import Stok (CSV)</span>
                </button>
                <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Input batch</span>
                </a>
            </div>
        @endif
    </header>

    <div class="ml-kpi-grid">
        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Batch berisiko</span>
                <span class="ml-kpi-icon is-risk" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 9v4M12 17h.01" />
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($atRiskCount ?? 0, 0, ',', '.') }} <span>batch</span></div>
            <div class="ml-kpi-meta">Kritis &le;30 hari atau tinggi &le;60 hari</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Stok tercatat</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($totalStockUnits ?? 0, 0, ',', '.') }} <span>unit</span></div>
            <div class="ml-kpi-meta">{{ number_format($totalBatchesCount ?? 0, 0, ',', '.') }} batch dalam tampilan ini</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Nilai surplus</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value ml-kpi-value-currency">Rp {{ number_format($totalWasteValue ?? 0, 0, ',', '.') }}</div>
            <div class="ml-kpi-meta">{{ number_format($totalWasteUnits ?? 0, 0, ',', '.') }} unit berpotensi tidak terserap</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Potensi limbah dicegah</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M19 11H5m14 0a7 7 0 01-7 7m7-7a7 7 0 00-7-7m-7 7a7 7 0 007 7m-7-7a7 7 0 017-7" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">~{{ number_format($estimatedWasteKg ?? 0, 1, ',', '.') }} <span>kg</span></div>
            <div class="ml-kpi-meta">Estimasi 0,085 kg per unit surplus</div>
        </article>
    </div>

    @if($topRiskBatch && ! empty($topCandidates))
        <section class="ml-rec-banner" aria-labelledby="rec-banner-title">
            <div class="ml-rec-banner-head">
                <div>
                    <span class="ml-rec-eyebrow">Peluang redistribusi prioritas</span>
                    <h2 class="ml-rec-title" id="rec-banner-title">
                        {{ $topRiskBatch->medicine->name }} &middot; Batch {{ $topRiskBatch->batch_number }}
                    </h2>
                    <p class="ml-rec-sub">
                        {{ number_format($topRiskBatch->projected_waste) }} unit berisiko kedaluwarsa dalam {{ $topRiskBatch->days_to_expiry }} hari.
                        Berikut faskes terdekat yang dapat menyerap:
                    </p>
                </div>
                <span class="ml-risk ml-risk-critical">Kritis</span>
            </div>

            <div class="ml-rec-grid">
                @foreach(array_slice($topCandidates, 0, 3) as $candidate)
                    @php
                        $intakeCap = $candidate['recipient_intake_cap'] ?? $candidate['recommended_quantity'];
                    @endphp
                    <article class="ml-rec-card">
                        <div class="ml-rec-top">
                            <span class="ml-rec-name">{{ $candidate['facility']->name }}</span>
                            <span class="ml-rec-score">{{ $candidate['match_score'] }}% cocok</span>
                        </div>
                        <div class="ml-rec-meta">
                            <span>Jarak {{ $candidate['distance_km'] }} km</span>
                            <span>Batas Alokasi: <strong>{{ number_format($intakeCap) }} {{ $topRiskBatch->medicine->unit }}</strong></span>
                        </div>

                        @if(! $user->isDinkesAuditor())
                            <a href="{{ route('transfers.propose', $topRiskBatch) }}" class="ml-btn-primary ml-btn-sm ml-rec-cta">
                                <span>Kirim usulan</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 12h14M13 6l6 6-6 6" />
                                </svg>
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>

            <p class="ml-rec-note">Rekomendasi dihitung dari kebutuhan penerima, jarak, dan kesiapan logistik. Keputusan akhir tetap berada pada petugas fasilitas.</p>
        </section>
    @endif

    <!-- Quick Filter Tabs (Bebas Emot & Selalu Terlihat Jelas Saat Ditekan) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs select-none">
        @php $isAllActive = ($currentTab === 'all' || !$currentTab); @endphp
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'all', 'page' => 1]) }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold border transition shrink-0"
           style="{{ $isAllActive ? 'background: #1D4ED8; color: #ffffff; border-color: #1D4ED8;' : 'background: #ffffff; color: #0B1F4B; border-color: #DBE5F5;' }}">
            <span>Semua Batch</span>
            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-extrabold"
                  style="{{ $isAllActive ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: #F1F5F9; color: #52627F;' }}">
                {{ $counts['all'] ?? 0 }}
            </span>
        </a>

        @php $isRiskActive = ($currentTab === 'risk'); @endphp
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'risk', 'page' => 1]) }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold border transition shrink-0"
           style="{{ $isRiskActive ? 'background: #D9480F; color: #ffffff; border-color: #D9480F;' : 'background: #ffffff; color: #0B1F4B; border-color: #DBE5F5;' }}">
            <span>Berisiko Kritis &amp; Tinggi</span>
            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-extrabold"
                  style="{{ $isRiskActive ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: #FFF4EC; color: #D9480F;' }}">
                {{ $counts['risk'] ?? 0 }}
            </span>
        </a>

        @php $isExpActive = ($currentTab === 'expiring'); @endphp
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'expiring', 'page' => 1]) }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold border transition shrink-0"
           style="{{ $isExpActive ? 'background: #D97706; color: #ffffff; border-color: #D97706;' : 'background: #ffffff; color: #0B1F4B; border-color: #DBE5F5;' }}">
            <span>Segera Kedaluwarsa (&le; 60 Hari)</span>
            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-extrabold"
                  style="{{ $isExpActive ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: #FFFBEB; color: #D97706;' }}">
                {{ $counts['expiring'] ?? 0 }}
            </span>
        </a>

        @php $isColdActive = ($currentTab === 'cold_chain'); @endphp
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'cold_chain', 'page' => 1]) }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold border transition shrink-0"
           style="{{ $isColdActive ? 'background: #0284C7; color: #ffffff; border-color: #0284C7;' : 'background: #ffffff; color: #0B1F4B; border-color: #DBE5F5;' }}">
            <span>Rantai Dingin</span>
            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-extrabold"
                  style="{{ $isColdActive ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: #F0F9FF; color: #0284C7;' }}">
                {{ $counts['cold_chain'] ?? 0 }}
            </span>
        </a>
    </div>

    <section class="ml-registry-panel" aria-labelledby="inventory-title">
        <div class="ml-section-header ml-section-header-split">
            <div>
                <h2 class="ml-section-title" id="inventory-title">Matriks inventaris</h2>
                <p class="ml-section-sub">
                    {{ number_format($batches->total(), 0, ',', '.') }} batch pada tampilan ini.
                </p>
            </div>
            @if(! $batches->isEmpty())
                <div class="ml-inventory-legend" role="group" aria-label="Klasifikasi risiko batch">
                    <span class="ml-risk ml-risk-critical">Kritis &le;30 hari</span>
                    <span class="ml-risk ml-risk-high">Tinggi &le;60 hari</span>
                    <span class="ml-risk ml-risk-medium">Sedang &le;90 hari</span>
                    <span class="ml-risk ml-risk-low">Rendah</span>
                </div>
            @endif
        </div>

        <form method="GET" action="{{ route('batches.index') }}" class="ml-registry-toolbar flex flex-wrap items-center gap-2" aria-label="Cari dan filter batch">
            @if($currentTab && $currentTab !== 'all')
                <input type="hidden" name="tab" value="{{ $currentTab }}">
            @endif

            <div class="ml-registry-search flex-1 min-w-[220px]">
                <label for="filter-search" class="ml-sr-only">Cari batch obat</label>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="M20 20l-3.5-3.5" />
                </svg>
                <input id="filter-search" name="search" type="search" class="ml-input" value="{{ $currentSearch }}" placeholder="Cari obat, nomor batch, atau lokasi rak">
            </div>

            @if($user->isDinkesAuditor() && isset($facilitiesList))
                <div class="ml-registry-filter">
                    <label for="filter-facility" class="ml-sr-only">Pilih fasilitas</label>
                    <select id="filter-facility" name="facility_id" class="ml-select" onchange="this.form.submit()">
                        <option value="">Semua fasilitas</option>
                        @foreach($facilitiesList as $facilityOption)
                            <option value="{{ $facilityOption->id }}" @selected((string) request('facility_id') === (string) $facilityOption->id)>
                                {{ $facilityOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="ml-registry-filter">
                <label for="filter-status" class="ml-sr-only">Status batch</label>
                <select id="filter-status" name="status" class="ml-select" onchange="this.form.submit()">
                    <option value="">Status: Semua</option>
                    <option value="ACTIVE" @selected($currentStatus === 'ACTIVE')>Aktif</option>
                    <option value="RESERVED" @selected($currentStatus === 'RESERVED')>Dicadangkan</option>
                    <option value="DEPLETED" @selected($currentStatus === 'DEPLETED')>Habis</option>
                    <option value="EXPIRED" @selected($currentStatus === 'EXPIRED')>Kedaluwarsa</option>
                </select>
            </div>

            <div class="ml-registry-filter">
                <label for="filter-sort" class="ml-sr-only">Urutan batch</label>
                <select id="filter-sort" name="sort" class="ml-select" onchange="this.form.submit()">
                    <option value="expiry_asc" @selected($currentSort === 'expiry_asc')>Sort: Kedaluwarsa Terdekat (FEFO)</option>
                    <option value="expiry_desc" @selected($currentSort === 'expiry_desc')>Sort: Kedaluwarsa Terjauh</option>
                    <option value="stock_desc" @selected($currentSort === 'stock_desc')>Sort: Stok Terbanyak</option>
                    <option value="stock_asc" @selected($currentSort === 'stock_asc')>Sort: Stok Tersedikit</option>
                    <option value="burn_rate_desc" @selected($currentSort === 'burn_rate_desc')>Sort: Laju Pakai Tertinggi</option>
                </select>
            </div>

            <button type="submit" class="ml-btn-primary ml-btn-sm">Terapkan</button>

            @if($hasFilters)
                <a href="{{ route('batches.index') }}" class="ml-btn-quiet ml-btn-sm" title="Hapus semua filter">Reset</a>
            @endif
        </form>

        @if($batches->isEmpty())
            <div class="ml-empty-state">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="ml-empty-title">Belum ada batch pada filter ini</p>
                <p class="ml-empty-copy">
                    @if($hasFilters)
                        Tidak ada batch yang cocok dengan pencarian atau status yang dipilih. Ubah filter untuk melihat data lain.
                    @else
                        Data batch yang Anda input akan langsung dianalisis untuk mendeteksi risiko limbah kedaluwarsa.
                    @endif
                </p>
                @if($hasFilters)
                    <a href="{{ route('batches.index') }}" class="ml-btn-quiet">Hapus filter</a>
                @elseif(! $user->isDinkesAuditor())
                    <a href="{{ route('batches.create') }}" class="ml-btn-primary">Input batch</a>
                @endif
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy ml-table-inventory">
                    <caption class="ml-sr-only">
                        Daftar batch obat beserta stok, sisa umur, proyeksi limbah, dan aksi redistribusi.
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col">Obat &amp; spesifikasi</th>
                            <th scope="col">Batch &amp; lokasi</th>
                            <th scope="col" class="is-right">Stok</th>
                            <th scope="col">Kedaluwarsa &amp; runway</th>
                            <th scope="col" class="is-right">Proyeksi limbah</th>
                            <th scope="col" class="is-right"><span class="ml-sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $batch)
                            @php
                                $risk = (string) $batch->risk_level;
                                $riskLabel = $riskLabels[$risk] ?? ucfirst(strtolower($risk));
                                $riskTone = strtolower($risk);
                                $daysToExpiry = (int) $batch->days_to_expiry;
                                $daysToDeplete = is_finite($batch->days_to_deplete) ? max(0, (float) $batch->days_to_deplete) : null;
                                $projectedWaste = (int) $batch->projected_waste;
                                $timelineDays = max(abs($daysToExpiry), (int) round($daysToDeplete ?? 0), 1);
                                $depletePercent = $daysToDeplete === null ? 100 : min(100, round($daysToDeplete / $timelineDays * 100, 1));
                                $expiryPercent = min(100, round(max(0, $daysToExpiry) / $timelineDays * 100, 1));
                                $isUrgent = in_array($risk, ['CRITICAL', 'HIGH'], true);
                                $simId = 'sim-'.$batch->id;
                            @endphp
                            <tr>
                                <td data-label="Obat">
                                    <div class="ml-cell-title">{{ $batch->medicine->name }}</div>
                                    <div class="ml-cell-meta">
                                        {{ $batch->medicine->dosage_form }} &middot; {{ $batch->medicine->category }}
                                        @if($batch->medicine->requires_cold_chain)
                                            <span class="ml-cold-chain" role="img" title="Perlu rantai dingin" aria-label="Perlu rantai dingin">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path d="M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9L4.9 19.1" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td data-label="Batch &amp; lokasi">
                                    <span class="ml-batch-code">{{ $batch->batch_number }}</span>
                                    <div class="ml-cell-meta">
                                        <svg class="ml-inline-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $batch->storage_location ?? 'Gudang Farmasi Utama' }}
                                    </div>
                                </td>

                                <td class="ml-cell-stock" data-label="Stok">
                                    <span class="ml-stock-value">{{ number_format($batch->current_stock) }}</span>
                                    <span class="ml-stock-unit">{{ $batch->medicine->unit }}</span>
                                    <div class="ml-cell-meta">~{{ $batch->daily_burn_rate }} unit/hari</div>
                                </td>

                                <td data-label="Kedaluwarsa &amp; runway">
                                    <div class="ml-runway-text">
                                        <span class="{{ $isUrgent ? 'is-risk' : 'is-safe' }}">
                                            {{ $daysToExpiry <= 0 ? 'Sudah kedaluwarsa' : number_format($daysToExpiry).' hari lagi' }}
                                        </span>
                                        <span class="ml-runway-dot">&middot;</span>
                                        <span class="ml-runway-deplete">
                                            {{ $daysToDeplete === null ? 'Stok tidak habis' : 'Habis ~'.number_format($daysToDeplete, 0).' hari' }}
                                        </span>
                                    </div>
                                    <div class="ml-runway-bar" aria-hidden="true">
                                        <div class="ml-runway-fill {{ $isUrgent ? 'is-risk' : '' }}" style="width: {{ $expiryPercent }}%;"></div>
                                        <div class="ml-runway-marker" style="left: {{ $depletePercent }}%;" title="Proyeksi stok habis"></div>
                                    </div>
                                    <button
                                        type="button"
                                        class="ml-simulator-toggle"
                                        id="sim-trigger-{{ $simId }}"
                                        aria-expanded="false"
                                        aria-controls="{{ $simId }}"
                                        onclick="toggleSimulator('{{ $simId }}')"
                                    >
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                            <path d="M12 5v14M5 12h14" />
                                        </svg>
                                        Simulasi laju konsumsi
                                    </button>
                                </td>

                                <td class="is-right" data-label="Proyeksi limbah">
                                    <div class="ml-waste-cell">
                                        <span class="ml-risk ml-risk-{{ $riskTone }}">{{ $riskLabel }}</span>
                                        @if($projectedWaste > 0)
                                            <span class="ml-waste-value">{{ number_format($projectedWaste) }} unit</span>
                                            <span class="ml-cell-meta">Rp {{ number_format($projectedWaste * (float) $batch->unit_price_idr, 0, ',', '.') }}</span>
                                        @else
                                            <span class="ml-waste-value is-safe">0 unit</span>
                                            <span class="ml-cell-meta">Tidak ada proyeksi limbah</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="is-right" data-label="Aksi">
                                    <div class="ml-inventory-actions">
                                        @if($projectedWaste > 0 && $batch->current_stock > 0 && ! $user->isDinkesAuditor())
                                            <a href="{{ route('transfers.propose', $batch) }}" class="ml-btn-primary ml-btn-sm">
                                                <span>Usulkan</span>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                                    <path d="M5 12h14M13 6l6 6-6 6" />
                                                </svg>
                                            </a>
                                        @endif

                                        @if(! $user->isDinkesAuditor())
                                            <a href="{{ route('batches.edit', $batch) }}" class="ml-btn-quiet ml-btn-sm">Edit</a>

                                            <form method="POST" action="{{ route('batches.destroy', $batch) }}" onsubmit='return confirm(@js("Apakah Anda yakin ingin menghapus data batch {$batch->batch_number}?"))'>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-btn-quiet ml-btn-sm is-danger">Hapus</button>
                                            </form>
                                        @else
                                            <span class="ml-cell-meta">Hanya baca</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <tr class="ml-simulator-row" id="{{ $simId }}" hidden>
                                <td colspan="6">
                                    <div class="ml-simulator">
                                        <div class="ml-simulator-head">
                                            <div>
                                                <p class="ml-page-eyebrow">Simulator keputusan</p>
                                                <h3 class="ml-simulator-title">Uji laju konsumsi {{ $batch->medicine->name }}</h3>
                                                <p class="ml-simulator-sub">Geser laju pemakaian untuk melihat dampaknya pada proyeksi limbah dan sisa umur stok.</p>
                                            </div>
                                            <button type="button" class="ml-icon-btn" onclick="toggleSimulator('{{ $simId }}')" aria-label="Tutup simulator">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path d="M18 6L6 18M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="ml-simulator-grid">
                                            <div>
                                                <label for="rate-{{ $simId }}" class="ml-simulator-label">
                                                    Laju konsumsi harian:
                                                    <output id="label-{{ $simId }}" class="ml-simulator-current">{{ $batch->daily_burn_rate }}</output>
                                                    unit/hari
                                                </label>
                                                <input
                                                    id="rate-{{ $simId }}"
                                                    type="range"
                                                    class="ml-range"
                                                    min="0.5"
                                                    max="{{ max(20, (float) $batch->daily_burn_rate * 3) }}"
                                                    step="0.5"
                                                    value="{{ $batch->daily_burn_rate }}"
                                                    aria-describedby="scale-{{ $simId }}"
                                                    oninput="runSimulation('{{ $simId }}', {{ (int) $batch->current_stock }}, {{ $daysToExpiry }}, this.value)"
                                                >
                                                <div class="ml-simulator-scale" id="scale-{{ $simId }}">
                                                    <span>0,5 unit/hari</span>
                                                    <span>{{ $batch->daily_burn_rate }} saat ini</span>
                                                    <span>{{ max(20, (float) $batch->daily_burn_rate * 3) }} unit/hari</span>
                                                </div>
                                            </div>

                                            <div class="ml-simulator-metrics">
                                                <div class="ml-simulator-metric">
                                                    <span class="ml-simulator-metric-label">Proyeksi limbah</span>
                                                    <span id="waste-{{ $simId }}" class="ml-simulator-metric-value {{ $projectedWaste > 0 ? 'is-risk' : 'is-safe' }}">
                                                        {{ number_format($projectedWaste) }} unit
                                                    </span>
                                                </div>
                                                <div class="ml-simulator-metric">
                                                    <span class="ml-simulator-metric-label">Sisa umur stok</span>
                                                    <span id="deplete-{{ $simId }}" class="ml-simulator-metric-value">
                                                        {{ $daysToDeplete === null ? 'Tidak habis' : number_format($daysToDeplete, 0).' hari' }}
                                                    </span>
                                                </div>
                                                <div class="ml-simulator-metric">
                                                    <span class="ml-simulator-metric-label">Status risiko</span>
                                                    <span id="risk-{{ $simId }}" class="ml-risk ml-risk-{{ $riskTone }}">{{ $riskLabel }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ml-pagination-wrap">
                {{ $batches->links() }}
            </div>
        @endif
    </section>

    <!-- Modal CSV Import -->
    <div id="ml-csv-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" onclick="document.getElementById('ml-csv-modal').classList.add('hidden')"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
                <!-- Header -->
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-[#0B1F4B]" id="modal-title">Impor Data Stok Obat (CSV)</h3>
                        <p class="text-xs text-[#52627F] mt-0.5">Unggah berkas spreadsheet/CSV inventaris untuk sinkronisasi massal</p>
                    </div>
                    <button type="button" onclick="document.getElementById('ml-csv-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('batches.importCsv') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf

                    <!-- Instructions & Template Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3.5 flex items-start justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <p class="font-bold text-[#1D4ED8]">Format Kolom CSV yang Didukung:</p>
                            <p class="text-slate-600 leading-relaxed text-[11.5px]">
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">nama_obat</code>, 
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">nomor_batch</code>, 
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">stok_saat_ini</code>, 
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">tanggal_kadaluwarsa</code>, 
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">laju_pemakaian_harian</code>
                            </p>
                        </div>
                        <a href="{{ route('batches.csvTemplate') }}" class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-blue-100/60 border border-blue-300 text-[#1D4ED8] font-bold rounded-lg text-[11px] shadow-2xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Template</span>
                        </a>
                    </div>

                    <!-- Dropzone File Input -->
                    <div class="border-2 border-dashed border-[#BFDBFE] hover:border-[#1D4ED8] bg-slate-50/70 hover:bg-blue-50/40 rounded-xl p-6 text-center transition cursor-pointer" onclick="document.getElementById('csv_file_input').click()">
                        <svg class="w-10 h-10 mx-auto text-[#1D4ED8] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-xs font-bold text-[#0B1F4B]">Pilih file CSV dari komputer Anda</p>
                        <p class="text-[11px] text-[#52627F] mt-0.5">Mendukung file .csv atau .txt (Maksimal 5 MB)</p>
                        <p id="selected-file-name" class="mt-2 text-xs font-semibold text-emerald-700 hidden"></p>
                        <input type="file" name="csv_file" id="csv_file_input" accept=".csv,.txt" required class="hidden" onchange="if(this.files[0]) { document.getElementById('selected-file-name').textContent = 'File terpilih: ' + this.files[0].name; document.getElementById('selected-file-name').classList.remove('hidden'); }">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('ml-csv-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-[#52627F] hover:bg-slate-100 rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Mulai Impor &amp; Kalkulasi</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSimulator(id) {
        const row = document.getElementById(id);
        if (!row) {
            return;
        }

        const trigger = document.getElementById('sim-trigger-' + id);
        const isOpening = row.hidden;

        row.hidden = !isOpening;

        if (trigger) {
            trigger.setAttribute('aria-expanded', String(isOpening));
        }
    }

    function runSimulation(id, currentStock, daysToExpiry, newBurnRate) {
        const burnRate = Number.parseFloat(newBurnRate);
        const numberFormatter = new Intl.NumberFormat('id-ID');
        const decimalFormatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 });

        document.getElementById('label-' + id).textContent = decimalFormatter.format(burnRate);

        const remainingDays = Math.max(0, daysToExpiry);
        const expectedConsumption = Math.floor(burnRate * remainingDays);
        const projectedWaste = Math.max(0, currentStock - expectedConsumption);
        const daysToDeplete = burnRate > 0 ? currentStock / burnRate : null;
        const wasteRatio = currentStock > 0 ? projectedWaste / currentStock : 0;

        let newRisk = 'LOW';
        let newRiskLabel = 'Rendah';

        if (projectedWaste > 0) {
            if (remainingDays <= 30) {
                newRisk = 'CRITICAL';
                newRiskLabel = 'Kritis';
            } else if (remainingDays <= 60 || wasteRatio >= 0.25) {
                newRisk = 'HIGH';
                newRiskLabel = 'Tinggi';
            } else if (remainingDays <= 90) {
                newRisk = 'MEDIUM';
                newRiskLabel = 'Sedang';
            }
        }

        const wasteElement = document.getElementById('waste-' + id);
        wasteElement.textContent = numberFormatter.format(projectedWaste) + ' unit';
        wasteElement.className = 'ml-simulator-metric-value ' + (projectedWaste > 0 ? 'is-risk' : 'is-safe');

        const depleteElement = document.getElementById('deplete-' + id);
        depleteElement.textContent = daysToDeplete === null
            ? 'Tidak habis'
            : decimalFormatter.format(daysToDeplete) + ' hari';

        const riskElement = document.getElementById('risk-' + id);
        riskElement.textContent = newRiskLabel;
        riskElement.className = 'ml-risk ml-risk-' + newRisk.toLowerCase();
    }
</script>
@endsection
