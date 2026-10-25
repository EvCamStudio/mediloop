@extends('layouts.mediloop', ['title' => 'Ringkasan Logistik'])

@section('content')
@php
    $batchCollection = $batches ?? collect();
    $overviewCollection = $overviewBatches ?? $batchCollection->filter(fn ($b) => in_array($b->risk_level, ['CRITICAL', 'HIGH', 'MEDIUM']));
    $totalBatches = $batchCollection->count();
    $totalUnits = $totalStockUnits ?? $batchCollection->sum('current_stock');
    $networkCount = isset($facilitiesList) ? $facilitiesList->count() : 0;

    $cCount = $criticalCount ?? $batchCollection->where('risk_level', 'CRITICAL')->count();
    $hCount = $highCount ?? $batchCollection->where('risk_level', 'HIGH')->count();
    $mCount = $mediumCount ?? $batchCollection->where('risk_level', 'MEDIUM')->count();
    $lCount = $lowCount ?? $batchCollection->where('risk_level', 'LOW')->count();
    $riskCount = $atRiskCount ?? ($cCount + $hCount + $mCount);

    $atRiskPercent = $totalBatches > 0 ? round(($riskCount / $totalBatches) * 100) : 0;
    $criticalPercent = $totalBatches > 0 ? round(($cCount / $totalBatches) * 100) : 0;
    $highPercent = $totalBatches > 0 ? round(($hCount / $totalBatches) * 100) : 0;
    $mediumPercent = $totalBatches > 0 ? round(($mCount / $totalBatches) * 100) : 0;
    $lowPercent = $totalBatches > 0 ? round(($lCount / $totalBatches) * 100) : 0;

    // SVG Donut calculation: radius = 48, circumference = 2 * pi * 48 = 301.593
    $circumference = 301.593;
    $critLen = $totalBatches > 0 ? ($cCount / $totalBatches) * $circumference : 0;
    $highLen = $totalBatches > 0 ? ($hCount / $totalBatches) * $circumference : 0;
    $medLen = $totalBatches > 0 ? ($mCount / $totalBatches) * $circumference : 0;
    $lowLen = $totalBatches > 0 ? ($lCount / $totalBatches) * $circumference : 0;

    $segments = [];
    $offset = 0;
    if ($cCount > 0) {
        $segments[] = [
            'color' => '#EF4444',
            'len' => $critLen,
            'offset' => $offset,
            'label' => 'Kritis',
        ];
        $offset += $critLen;
    }
    if ($hCount > 0) {
        $segments[] = [
            'color' => '#F97316',
            'len' => $highLen,
            'offset' => $offset,
            'label' => 'Tinggi',
        ];
        $offset += $highLen;
    }
    if ($mCount > 0) {
        $segments[] = [
            'color' => '#F59E0B',
            'len' => $medLen,
            'offset' => $offset,
            'label' => 'Sedang',
        ];
        $offset += $medLen;
    }
    if ($lCount > 0) {
        $segments[] = [
            'color' => '#10B981',
            'len' => $lowLen,
            'offset' => $offset,
            'label' => 'Aman',
        ];
        $offset += $lowLen;
    }
    $hasMultiple = count($segments) > 1;
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

    <!-- MAIN DASHBOARD SPLIT: OVERVIEW TABLE (LEFT) + MODERN STATS & DONUT CHART (RIGHT) -->
    <div class="ml-dashboard-main-grid">
        <section class="ml-section ml-overview-table-card" aria-labelledby="forecast-title">
            <div class="ml-section-header ml-section-header-split">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <h2 class="ml-section-title" id="forecast-title">Prediksi risiko kedaluwarsa</h2>
                        @if($overviewCollection->isNotEmpty())
                            <span class="ml-badge-count">{{ $overviewCollection->count() }} berisiko</span>
                        @endif
                    </div>
                    <p class="ml-section-sub">Batch berisiko (Kritis, Tinggi, Sedang) yang memerlukan mitigasi redistribusi.</p>
                </div>
                <a href="{{ route('batches.index') }}" class="ml-section-link">
                    <span>Lihat semua stok</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>

            <div class="ml-table-responsive">
                <table class="ml-table ml-table-dashboard">
                    <caption class="ml-sr-only">
                        Daftar batch obat aktif berisiko kedaluwarsa beserta stok, estimasi habis, dan aksi redistribusi.
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col">Obat &amp; batch</th>
                            <th scope="col">Stok fisik</th>
                            <th scope="col">Sisa umur batch</th>
                            <th scope="col" class="is-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overviewCollection as $batch)
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
                                    <div class="ml-runway-text" style="display: flex; flex-direction: column; gap: 2px;">
                                        <span class="{{ $isLowRisk ? 'is-safe' : 'is-risk' }}" style="font-weight: 600;">
                                            Kedaluwarsa {{ $batch->days_to_expiry }} hari
                                        </span>
                                        <span style="font-size: 11px; color: var(--ml-muted);">
                                            stok habis {{ $daysToDeplete === null ? 'tidak habis' : number_format($daysToDeplete, 0).' hari' }}
                                        </span>
                                    </div>
                                    <div class="ml-runway" aria-hidden="true" style="margin-top: 6px;">
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
                                    <div class="ml-empty-state" style="padding: 32px 16px;">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="ml-empty-title" style="margin-top: 10px; font-size: 15px;">Semua batch dalam kondisi aman</div>
                                        <p class="ml-empty-copy" style="max-width: 340px; margin: 4px auto 14px;">
                                            Tidak ada batch obat aktif yang berisiko kedaluwarsa saat ini. Seluruh stok terpantau aman terkendali.
                                        </p>
                                        <a href="{{ route('batches.index') }}" class="ml-btn-quiet ml-btn-sm">
                                            <span>Lihat semua stok aktif</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- RIGHT: STATISTIK & KOMPOSISI RISIKO (DONUT CHART) -->
        <aside class="ml-section ml-stats-card" aria-labelledby="risk-stats-title">
            <div class="ml-stats-header">
                <div>
                    <h2 class="ml-stats-title" id="risk-stats-title">Komposisi Risiko Stok</h2>
                    <p class="ml-stats-sub">Distribusi dari total {{ $totalBatches }} batch aktif faskes</p>
                </div>
                <span class="ml-stats-badge {{ $riskCount > 0 ? 'is-warning' : 'is-safe' }}">
                    {{ $riskCount > 0 ? $riskCount . ' Berisiko' : 'Semua Aman' }}
                </span>
            </div>

            <div class="ml-stats-body">
                <div class="ml-donut-wrapper">
                    <svg class="ml-donut-svg" viewBox="0 0 140 140" width="124" height="124" role="img" aria-label="Grafik persentase risiko stok obat">
                        <circle cx="70" cy="70" r="48" class="ml-donut-track" />
                        <g transform="rotate(-90 70 70)">
                            @foreach($segments as $seg)
                                @php
                                    $dashLen = $hasMultiple ? max(1, $seg['len'] - 2.5) : $seg['len'];
                                @endphp
                                <circle
                                    cx="70"
                                    cy="70"
                                    r="48"
                                    fill="none"
                                    stroke="{{ $seg['color'] }}"
                                    stroke-width="12"
                                    stroke-dasharray="{{ number_format($dashLen, 2, '.', '') }} {{ number_format($circumference, 2, '.', '') }}"
                                    stroke-dashoffset="{{ number_format(-$seg['offset'], 2, '.', '') }}"
                                    stroke-linecap="butt"
                                />
                            @endforeach
                        </g>
                        <!-- Center label -->
                        @if($riskCount > 0)
                            <text x="70" y="65" text-anchor="middle" class="ml-donut-center-val">{{ $atRiskPercent }}%</text>
                            <text x="70" y="81" text-anchor="middle" class="ml-donut-center-lbl">Berisiko</text>
                        @else
                            <text x="70" y="65" text-anchor="middle" class="ml-donut-center-val is-safe">100%</text>
                            <text x="70" y="81" text-anchor="middle" class="ml-donut-center-lbl is-safe">Aman</text>
                        @endif
                    </svg>
                </div>

                <div class="ml-stats-breakdown">
                    <div class="ml-breakdown-row">
                        <div class="ml-breakdown-info">
                            <span class="ml-breakdown-dot is-critical"></span>
                            <span class="ml-breakdown-name">Kritis</span>
                        </div>
                        <div class="ml-breakdown-nums">
                            <span class="ml-breakdown-count">{{ $cCount }} batch</span>
                            <span class="ml-breakdown-pct">{{ $criticalPercent }}%</span>
                        </div>
                    </div>

                    <div class="ml-breakdown-row">
                        <div class="ml-breakdown-info">
                            <span class="ml-breakdown-dot is-high"></span>
                            <span class="ml-breakdown-name">Tinggi</span>
                        </div>
                        <div class="ml-breakdown-nums">
                            <span class="ml-breakdown-count">{{ $hCount }} batch</span>
                            <span class="ml-breakdown-pct">{{ $highPercent }}%</span>
                        </div>
                    </div>

                    <div class="ml-breakdown-row">
                        <div class="ml-breakdown-info">
                            <span class="ml-breakdown-dot is-medium"></span>
                            <span class="ml-breakdown-name">Sedang</span>
                        </div>
                        <div class="ml-breakdown-nums">
                            <span class="ml-breakdown-count">{{ $mCount }} batch</span>
                            <span class="ml-breakdown-pct">{{ $mediumPercent }}%</span>
                        </div>
                    </div>

                    <div class="ml-breakdown-row">
                        <div class="ml-breakdown-info">
                            <span class="ml-breakdown-dot is-low"></span>
                            <span class="ml-breakdown-name">Aman</span>
                        </div>
                        <div class="ml-breakdown-nums">
                            <span class="ml-breakdown-count">{{ $lCount }} batch</span>
                            <span class="ml-breakdown-pct">{{ $lowPercent }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ml-stats-footer">
                <div class="ml-stats-footer-metric">
                    <span class="ml-stats-footer-lbl">Total Nilai Berisiko:</span>
                    <span class="ml-stats-footer-val">Rp {{ number_format($totalProjectedWasteValue ?? 0, 0, ',', '.') }}</span>
                </div>
                <p class="ml-stats-footer-tip">
                    @if($cCount > 0)
                        Prioritas utama: Terdapat <strong>{{ $cCount }} batch kritis</strong> yang mendesak untuk ditransfer segera.
                    @elseif($hCount > 0)
                        Disarankan: Terdapat <strong>{{ $hCount }} batch risiko tinggi</strong> yang dapat ditawarkan ke faskes sekitar.
                    @elseif($mCount > 0)
                        Dalam pemantauan: Batch risiko sedang terpantau stabil.
                    @else
                        Kondisi prima: Seluruh batch obat berada dalam pemakaian aman terkendali.
                    @endif
                </p>
            </div>
        </aside>
    </div>

    @if(isset($topRiskBatch))
        <section class="ml-section ml-rec-band" aria-labelledby="match-title">
            <div class="ml-section-header">
                <div>
                    <h2 class="ml-section-title" id="match-title">Rekomendasi Redistribusi Obat</h2>
                    <p class="ml-section-sub">
                        Kandidat faskes penerima yang paling cocok untuk obat <strong>{{ $topRiskBatch->medicine->name }}</strong>
                        (Nomor batch: <code>{{ $topRiskBatch->batch_number }}</code>).
                        Skor kecocokan dihitung dari: Kebutuhan riil (50%) &middot; Jarak terdekat (30%) &middot; Kesiapan rantai dingin (20%).
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
                        $cap = (int) ($candidate['recipient_intake_cap'] ?? 0);
                    @endphp
                    <article class="ml-rec-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <!-- Header Obat pada Kartu -->
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                                <span style="font-size: 11px; font-weight: 700; color: #1d4ed8; background: #eaf1ff; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                                    {{ $topRiskBatch->medicine->name }}
                                </span>
                                <span style="font-size: 10.5px; color: #64748b; font-family: monospace;">Batch {{ $topRiskBatch->batch_number }}</span>
                            </div>

                            <div class="ml-rec-top">
                                <span class="ml-rec-rank" aria-hidden="true">{{ $index + 1 }}</span>
                                <div class="ml-rec-body">
                                    <div class="ml-rec-name" style="font-weight: 700; color: #0b1f4b;">{{ $candidate['facility']->name }}</div>
                                    <div class="ml-rec-meta">
                                        {{ $facilityType }}
                                        &middot; {{ $candidate['distance_km'] }} km dari lokasi Anda
                                    </div>
                                </div>
                                <span class="ml-rec-score" title="Skor = kebutuhan 50% + jarak 30% + logistik 20%">
                                    {{ $candidate['match_score'] }}% Cocok
                                </span>
                            </div>

                            <div class="ml-rec-facts" style="margin-top: 10px;">
                                @if($cap > 0)
                                    <span style="font-weight: 600; color: #0f766e;">Kapasitas serap: {{ number_format($cap) }} unit</span>
                                @else
                                    <span style="font-weight: 500; color: #64748b;">Siap menerima transfer antar-faskes</span>
                                @endif
                            </div>
                        </div>

                        @if(! $user->isDinkesAuditor())
                            <a href="{{ route('transfers.propose', ['batch' => $topRiskBatch, 'recipient_id' => $candidate['facility']->id]) }}" class="ml-btn-primary ml-btn-sm ml-rec-cta" style="margin-top: 14px;">
                                <span>Ajukan transfer ke faskes ini</span>
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
