@extends('layouts.mediloop', ['title' => 'Overview Dashboard - MediLoop'])

@section('content')
<div class="ml-dashboard">

    {{-- Header & Action Row --}}
    <header class="ml-page-head">
        <div>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Ringkasan Logistik &amp; Prediksi Kedaluwarsa</h1>
                <span class="ml-facility-chip">{{ $facility?->name ?? 'Fasilitas Kesehatan' }}</span>
            </div>
            <p class="ml-page-sub">
                Sistem pendukung keputusan pemantauan stok farmasi dan redistribusi antarfaskes.
            </p>
        </div>

        @if (! ($facilityMissing ?? false))
            <div class="ml-page-actions">
                <a href="{{ route('batches.index') }}" class="ml-btn-quiet">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                    <span>Kelola Semua Stok</span>
                </a>
                @if(! $user->isDinkesAuditor())
                    <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v16m8-8H4"/></svg>
                        <span>Input Stok Obat Baru</span>
                    </a>
                @endif
            </div>
        @endif
    </header>

    @if ($facilityMissing ?? false)
        <section class="ml-section" role="status"
            style="padding: 20px 24px; border-left: 4px solid #B7791F; background: #FFFBEB; margin-bottom: 20px;">
            <div style="display: flex; gap: 14px; align-items: flex-start;">
                <div style="flex: 0 0 auto; width: 36px; height: 36px; border-radius: 9px; background: #FEF3C7; color: #92400E; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <div>
                    <h2 class="ml-section-title" style="margin: 0 0 4px;">Akun Belum Terhubung ke Fasilitas</h2>
                    <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #78350F;">
                        Data stok antar-faskes tidak ditampilkan sampai akun Anda dihubungkan ke fasilitas kesehatan
                        terverifikasi. Hubungi administrator platform untuk menyelesaikan verifikasi.
                    </p>
                </div>
            </div>
        </section>
    @endif

    {{-- 4 STAT CARDS --}}
    <div class="ml-stat-grid">

        {{-- At-Risk Batches --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">At-Risk Batches</div>
            <div style="margin-bottom: 8px;">
                <span class="ml-stat-badge">{{ $atRiskCount ?? 0 }} Batches</span>
            </div>
            @php
                $highRiskCount = isset($batches) ? $batches->filter(fn($b) => $b->risk_level === 'HIGH' || $b->risk_level === 'CRITICAL')->count() : 0;
                $watchRiskCount = ($atRiskCount ?? 0) - $highRiskCount;
            @endphp
            <div class="ml-stat-sub">
                <b>{{ $highRiskCount }} High Risk</b>
                &nbsp;·&nbsp;
                {{ $watchRiskCount > 0 ? $watchRiskCount : 0 }} Watch
            </div>
        </div>

        {{-- Redistributed Medicine --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Redistributed Medicine</div>
            <div class="ml-stat-sublabel">SDG 3 &amp; 12 impact</div>
            <div class="ml-stat-value">{{ number_format(isset($batches) ? $batches->sum('current_stock') : 0) }} Units</div>
            <div class="ml-stat-sub" style="color: var(--ml-subtle);">{{ isset($batches) ? $batches->count() : 0 }} batches total</div>
        </div>

        {{-- Prevented Waste Value --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Prevented Waste Value</div>
            <div style="margin-bottom: 8px;">
                <span class="ml-stat-badge-blue">Rp {{ number_format($totalProjectedWasteValue ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="ml-stat-sub" style="color: var(--ml-subtle);">Saved</div>
        </div>

        {{-- Active Network Partners --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Active Network Partners</div>
            <div class="ml-stat-value" style="font-size: 26px; margin-top: 8px;">
                {{ isset($facilitiesList) ? $facilitiesList->count() : 0 }} Health Facilities
            </div>
        </div>
    </div>

    {{-- FORECAST TABLE --}}
    <section class="ml-section" aria-labelledby="forecast-title">
        <div class="ml-section-header">
            <div>
                <h2 class="ml-section-title" id="forecast-title">Prediksi Risiko Kedaluwarsa &amp; Rekomendasi Match</h2>
                <p class="ml-section-sub">
                    Dihitung otomatis: hari hingga kedaluwarsa dibandingkan laju pemakaian harian faskes.
                </p>
            </div>
        </div>

        <div class="ml-table-responsive">
            <table class="ml-table ml-table-airy">
                <caption class="ml-sr-only">
                    Daftar batch obat aktif beserta stok, sisa umur, risiko kedaluwarsa, dan aksi redistribusi.
                </caption>
                <thead>
                    <tr>
                        <th scope="col">Obat &amp; Batch</th>
                        <th scope="col" class="is-right">Stok Fisik</th>
                        <th scope="col">Sisa Umur Batch</th>
                        <th scope="col" class="is-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches ?? [] as $batch)
                        @php
                            $daysToDeplete = is_finite($batch->days_to_deplete) ? max(0, (float) $batch->days_to_deplete) : null;
                            $daysToExpiry = max(1, (int) $batch->days_to_expiry);
                            $timelineDays = max($daysToExpiry, $daysToDeplete ?? $daysToExpiry);
                            $depletePercent = $daysToDeplete === null ? 100 : min(100, round($daysToDeplete / $timelineDays * 100, 1));
                            $expiryPercent = min(100, round($daysToExpiry / $timelineDays * 100, 1));
                            $isLowRisk = $batch->risk_level === 'LOW';
                        @endphp
                        <tr>
                            <td>
                                <div class="ml-cell-title">{{ $batch->medicine->name }}</div>
                                <div class="ml-cell-meta">
                                    {{ $batch->medicine->dosage_form }} &middot; {{ $batch->medicine->category }}
                                    @if($batch->medicine->requires_cold_chain)
                                        <span class="ml-cold-chain" title="Perlu rantai dingin" aria-label="Perlu rantai dingin">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9L4.9 19.1"/></svg>
                                        </span>
                                    @endif
                                </div>
                                <div class="ml-cell-meta ml-cell-mono">
                                    Batch {{ $batch->batch_number }} &middot; Exp {{ $batch->expiry_date->format('d M Y') }}
                                </div>
                            </td>
                            <td class="ml-cell-stock">
                                <span class="ml-stock-value">{{ number_format($batch->current_stock) }}</span>
                                <span class="ml-stock-unit">{{ $batch->medicine->unit }}</span>
                            </td>
                            <td>
                                <div class="ml-runway-text">
                                    <span class="{{ $isLowRisk ? 'is-safe' : 'is-risk' }}">
                                        Kedaluwarsa {{ $batch->days_to_expiry }} hari
                                    </span>
                                    <span class="ml-runway-sep" aria-hidden="true">&middot;</span>
                                    <span>
                                        stok habis {{ $daysToDeplete === null ? 'tidak habis' : number_format($daysToDeplete, 0).' hari' }}
                                    </span>
                                </div>
                                <div class="ml-runway" aria-hidden="true">
                                    <span class="ml-runway-fill" style="width: {{ $depletePercent }}%"></span>
                                    <span class="ml-runway-mark" style="left: {{ $expiryPercent }}%"></span>
                                </div>
                            </td>
                            <td>
                                <div class="ml-row-actions">
                                    <span class="ml-risk ml-risk-{{ strtolower($batch->risk_level) }}">
                                        {{ $batch->risk_level }}
                                    </span>

                                    @if(! $user->isDinkesAuditor() && $batch->projected_waste > 0 && $batch->current_stock > 0)
                                        <a href="{{ route('transfers.propose', $batch) }}" class="ml-btn-quiet ml-btn-sm">
                                            <span>Cari Penerima</span>
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                        </a>
                                    @else
                                        <a href="{{ route('batches.edit', $batch) }}" class="ml-btn-quiet ml-btn-sm">
                                            Detail
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="ml-empty-state">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <div class="ml-empty-title">Belum Ada Data Stok di Fasilitas Ini</div>
                                    <p class="ml-empty-copy">
                                        Data obat yang Anda input akan langsung dianalisis oleh mesin prediksi untuk mendeteksi risiko limbah kedaluwarsa.
                                    </p>
                                    @if(! $user->isDinkesAuditor())
                                        <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                                            Input Stok Obat Sekarang
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- MATCH RECOMMENDATIONS --}}
    @if(isset($topRiskBatch))
        <section class="ml-section ml-rec-band" aria-labelledby="match-title">
            <div class="ml-section-header">
                <div>
                    <h2 class="ml-section-title" id="match-title">Rekomendasi Match Redistribusi</h2>
                    <p class="ml-section-sub">
                        Kandidat untuk <strong>{{ $topRiskBatch->medicine->name }}</strong>
                        (batch {{ $topRiskBatch->batch_number }}).
                        Skor: kebutuhan 50% &middot; jarak 30% &middot; logistik 20%.
                    </p>
                </div>
            </div>

            <div class="ml-rec-grid">
                @forelse($matchCandidates ?? [] as $index => $candidate)
                    @php
                        $facilityType = match (strtoupper($candidate['facility']->type)) {
                            'RSUD' => 'RSUD',
                            'PUSKESMAS' => 'Puskesmas',
                            'KLINIK' => 'Klinik',
                            'DINKES' => 'Dinkes',
                            default => ucwords(strtolower(str_replace('_', ' ', $candidate['facility']->type))),
                        };
                    @endphp
                    <article class="ml-rec-card">
                        <div class="ml-rec-top">
                            <span class="ml-rec-rank" aria-hidden="true">{{ $index + 1 }}</span>
                            <div class="ml-rec-body">
                                <div class="ml-rec-name">{{ $candidate['facility']->name }}</div>
                                <div class="ml-rec-meta">
                                    {{ $facilityType }}
                                    &middot; {{ $candidate['distance_km'] }} km
                                </div>
                            </div>
                            <span class="ml-rec-score" title="Skor = kebutuhan 50% + jarak 30% + logistik 20%">
                                {{ $candidate['match_score'] }}%
                            </span>
                        </div>

                        <div class="ml-rec-facts">
                            <span>Kapasitas {{ $candidate['recipient_intake_cap'] }} unit</span>
                        </div>

                        @if(! $user->isDinkesAuditor())
                            <a href="{{ route('transfers.propose', $topRiskBatch) }}" class="ml-btn-primary ml-btn-sm ml-rec-cta">
                                <span>Ajukan Transfer</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endif
                    </article>
                @empty
                    <div class="ml-rec-empty">
                        @if(isset($topRiskBatch))
                            Belum ada kandidat faskes penerima yang memenuhi syarat rantai dingin dan jarak.
                        @else
                            Belum ada batch berisiko kedaluwarsa. Tambahkan stok batch untuk mendapatkan rekomendasi transfer otomatis.
                        @endif
                    </div>
                @endforelse
            </div>

            <p class="ml-rec-note">
                Rekomendasi bersifat saran. Setiap transfer tetap memerlukan persetujuan petugas faskes.
            </p>
        </section>
    @endif
</div>
@endsection
