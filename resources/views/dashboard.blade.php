@extends('layouts.mediloop', ['title' => 'Ringkasan Logistik'])

@section('content')
@php
    $batchCollection = $batches ?? collect();
    $highRiskCount = $batchCollection->filter(fn ($batch) => in_array($batch->risk_level, ['HIGH', 'CRITICAL'], true))->count();
    $watchRiskCount = max(0, ($atRiskCount ?? 0) - $highRiskCount);
    $totalUnits = $batchCollection->sum('current_stock');
    $networkCount = isset($facilitiesList) ? $facilitiesList->count() : 0;
@endphp

<div class="ml-dashboard">
    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">Pusat kendali fasilitas</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Ringkasan logistik</h1>
                <span class="ml-facility-chip">{{ $facility?->name ?? 'Fasilitas kesehatan' }}</span>
            </div>
            <p class="ml-page-sub">Pantau risiko kedaluwarsa dan peluang redistribusi antar fasilitas.</p>
        </div>

        @if (! ($facilityMissing ?? false))
            <div class="ml-page-actions">
                <a href="{{ route('batches.index') }}" class="ml-btn-quiet">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                    <span>Kelola stok</span>
                </a>
                @if(! $user->isDinkesAuditor())
                    <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v16m8-8H4"/></svg>
                        <span>Input batch</span>
                    </a>
                @endif
            </div>
        @endif
    </header>

    @if ($facilityMissing ?? false)
        <div class="ml-alert ml-alert-warning" role="status">
            <strong>Akun belum terhubung ke fasilitas</strong>
            <p>Data stok antar fasilitas tidak ditampilkan sampai akun Anda dihubungkan ke fasilitas terverifikasi. Hubungi administrator platform untuk menyelesaikan verifikasi.</p>
        </div>
    @endif

    <div class="ml-kpi-grid">
        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Batch berisiko</span>
                <span class="ml-kpi-icon is-risk" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4M12 17h.01" /><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($atRiskCount ?? 0, 0, ',', '.') }} <span>batch</span></div>
            <div class="ml-kpi-meta">
                <span class="ml-kpi-emphasis">{{ $highRiskCount }} risiko tinggi</span>
                <span class="ml-meta-dot">·</span>
                {{ $watchRiskCount }} dipantau
            </div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Stok aktif</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($totalUnits, 0, ',', '.') }} <span>unit</span></div>
            <div class="ml-kpi-meta">{{ number_format($batchCollection->count(), 0, ',', '.') }} batch tercatat</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Nilai berisiko</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value ml-kpi-value-currency">Rp {{ number_format($totalProjectedWasteValue ?? 0, 0, ',', '.') }}</div>
            <div class="ml-kpi-meta">Proyeksi nilai yang dapat diselamatkan</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Jaringan fasilitas</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13A4 4 0 0116 11" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($networkCount, 0, ',', '.') }} <span>fasilitas</span></div>
            <div class="ml-kpi-meta">Terhubung untuk redistribusi</div>
        </article>
    </div>

    <section class="ml-section" aria-labelledby="forecast-title">
        <div class="ml-section-header ml-section-header-split">
            <div>
                <h2 class="ml-section-title" id="forecast-title">Prediksi risiko kedaluwarsa</h2>
                <p class="ml-section-sub">Hari hingga kedaluwarsa dibandingkan laju pemakaian harian fasilitas.</p>
            </div>
            <a href="{{ route('batches.index') }}" class="ml-section-link">Lihat semua stok</a>
        </div>

        <div class="ml-table-responsive">
            <table class="ml-table ml-table-airy">
                <caption class="ml-sr-only">
                    Daftar batch obat aktif beserta stok, sisa umur, risiko kedaluwarsa, dan aksi redistribusi.
                </caption>
                <thead>
                    <tr>
                        <th scope="col">Obat &amp; batch</th>
                        <th scope="col" class="is-right">Stok fisik</th>
                        <th scope="col">Sisa umur batch</th>
                        <th scope="col" class="is-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batchCollection as $batch)
                        @php
                            $daysToDeplete = is_finite($batch->days_to_deplete) ? max(0, (float) $batch->days_to_deplete) : null;
                            $daysToExpiry = max(1, (int) $batch->days_to_expiry);
                            $timelineDays = max($daysToExpiry, $daysToDeplete ?? $daysToExpiry);
                            $depletePercent = $daysToDeplete === null ? 100 : min(100, round($daysToDeplete / $timelineDays * 100, 1));
                            $expiryPercent = min(100, round($daysToExpiry / $timelineDays * 100, 1));
                            $isLowRisk = $batch->risk_level === 'LOW';
                            $riskLabel = match ($batch->risk_level) {
                                'CRITICAL' => 'Kritis',
                                'HIGH' => 'Tinggi',
                                'MEDIUM' => 'Sedang',
                                'LOW' => 'Rendah',
                                default => ucfirst(strtolower((string) $batch->risk_level)),
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="ml-cell-title">{{ $batch->medicine->name }}</div>
                                <div class="ml-cell-meta">
                                    {{ $batch->medicine->dosage_form }} &middot; {{ $batch->medicine->category }}
                                    @if($batch->medicine->requires_cold_chain)
                                        <span class="ml-cold-chain" role="img" title="Perlu rantai dingin" aria-label="Perlu rantai dingin">
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
                                        {{ $riskLabel }}
                                    </span>

                                    @if(! $user->isDinkesAuditor() && $batch->projected_waste > 0 && $batch->current_stock > 0)
                                        <a href="{{ route('transfers.propose', $batch) }}" class="ml-btn-quiet ml-btn-sm">
                                            <span>Cari penerima</span>
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
                                    <div class="ml-empty-title">Belum ada data stok</div>
                                    <p class="ml-empty-copy">
                                        Data obat yang Anda input akan langsung dianalisis untuk mendeteksi risiko limbah kedaluwarsa.
                                    </p>
                                    @if(! $user->isDinkesAuditor())
                                        <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                                            Input stok obat
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

    @if(isset($topRiskBatch))
        <section class="ml-section ml-rec-band" aria-labelledby="match-title">
            <div class="ml-section-header">
                <div>
                    <h2 class="ml-section-title" id="match-title">Rekomendasi match redistribusi</h2>
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
                                <span>Ajukan transfer</span>
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
