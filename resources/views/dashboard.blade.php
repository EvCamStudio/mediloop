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

    // SVG Donut calculation: radius = 80, circumference = 2 * pi * 80 = 502.655
    $circumference = 502.655;
    $critLen = $totalBatches > 0 ? ($cCount / $totalBatches) * $circumference : 0;
    $highLen = $totalBatches > 0 ? ($hCount / $totalBatches) * $circumference : 0;
    $medLen = $totalBatches > 0 ? ($mCount / $totalBatches) * $circumference : 0;
    $lowLen = $totalBatches > 0 ? ($lCount / $totalBatches) * $circumference : 0;

    $segments = [];
    $offset = 0;
    if ($cCount > 0) {
        $segments[] = [
            'color' => '#DC2626',
            'len' => $critLen,
            'offset' => $offset,
            'label' => 'Kritis',
            'count' => $cCount,
            'pct' => $criticalPercent,
        ];
        $offset += $critLen;
    }
    if ($hCount > 0) {
        $segments[] = [
            'color' => '#EA580C',
            'len' => $highLen,
            'offset' => $offset,
            'label' => 'Tinggi',
            'count' => $hCount,
            'pct' => $highPercent,
        ];
        $offset += $highLen;
    }
    if ($mCount > 0) {
        $segments[] = [
            'color' => '#D97706',
            'len' => $medLen,
            'offset' => $offset,
            'label' => 'Sedang',
            'count' => $mCount,
            'pct' => $mediumPercent,
        ];
        $offset += $medLen;
    }
    if ($lCount > 0) {
        $segments[] = [
            'color' => '#16A34A',
            'len' => $lowLen,
            'offset' => $offset,
            'label' => 'Aman',
            'count' => $lCount,
            'pct' => $lowPercent,
        ];
        $offset += $lowLen;
    }
    $hasMultiple = count($segments) > 1;

    // Dynamic Condition Colors & Status
    if ($cCount > 0) {
        $statusThemeColor = '#DC2626';
        $statusBgColor = '#FEF2F2';
        $statusBorderColor = '#FECACA';
        $statusTextColor = '#991B1B';
        $statusTitle = 'Risiko Kritis';
        $statusSub = $cCount . ' batch kritis mendesak';
    } elseif ($hCount > 0) {
        $statusThemeColor = '#EA580C';
        $statusBgColor = '#FFF7ED';
        $statusBorderColor = '#FED7AA';
        $statusTextColor = '#C2410C';
        $statusTitle = 'Risiko Tinggi';
        $statusSub = $hCount . ' batch risiko tinggi';
    } elseif ($mCount > 0) {
        $statusThemeColor = '#D97706';
        $statusBgColor = '#FFFBEB';
        $statusBorderColor = '#FDE68A';
        $statusTextColor = '#B45309';
        $statusTitle = 'Risiko Sedang';
        $statusSub = $mCount . ' batch risiko sedang';
    } else {
        $statusThemeColor = '#16A34A';
        $statusBgColor = '#F0FDF4';
        $statusBorderColor = '#BBF7D0';
        $statusTextColor = '#15803D';
        $statusTitle = 'Kondisi Aman';
        $statusSub = 'Seluruh batch aman';
    }
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
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01" /><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($riskCount, 0, ',', '.') }} <span>batch</span></div>
            <div class="ml-kpi-meta">
                @if($riskCount > 0)
                    <span class="ml-kpi-emphasis">{{ $cCount }} kritis</span>
                    <span class="ml-meta-dot">·</span>
                    {{ $hCount }} tinggi
                    <span class="ml-meta-dot">·</span>
                    {{ $mCount }} sedang
                @else
                    <span>Seluruh batch aman</span>
                @endif
            </div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Stok aktif</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($totalUnits, 0, ',', '.') }} <span>unit</span></div>
            <div class="ml-kpi-meta">{{ number_format($totalBatches, 0, ',', '.') }} batch aktif tercatat</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Nilai berisiko</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value ml-kpi-value-currency">Rp {{ number_format($totalProjectedWasteValue ?? 0, 0, ',', '.') }}</div>
            <div class="ml-kpi-meta">Potensi limbah kedaluwarsa</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Jaringan faskes</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13A4 4 0 0116 11" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($networkCount, 0, ',', '.') }} <span>faskes</span></div>
            <div class="ml-kpi-meta">Terhubung aktif redistribusi</div>
        </article>
    </div>

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
                            <th scope="col" class="is-left">Aksi</th>
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
                                $isSelectedBatch = isset($selectedBatch) && $selectedBatch->id === $batch->id;
                            @endphp
                            <tr id="dashboard-batch-row-{{ $batch->id }}"
                                class="ml-dashboard-batch-row {{ $isSelectedBatch ? 'is-selected-batch' : '' }}"
                                onclick="selectRecBatch({{ $batch->id }})"
                                style="cursor: pointer;"
                                title="Klik untuk melihat rekomendasi redistribusi obat {{ $batch->medicine->name }}">
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
                                            <button type="button"
                                                class="ml-btn-primary ml-btn-sm"
                                                onclick="event.stopPropagation(); selectRecBatch({{ $batch->id }}); document.getElementById('match-title').scrollIntoView({ behavior: 'smooth', block: 'start' });"
                                                title="Lihat faskes yang membutuhkan obat ini">
                                                <span>Rekomendasi</span>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                        @else
                                            <a href="{{ route('batches.edit', $batch) }}" class="ml-btn-quiet ml-btn-sm" onclick="event.stopPropagation();">
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
                <span class="ml-stats-badge" style="background: {{ $statusBgColor }}; color: {{ $statusTextColor }}; border: 1px solid {{ $statusBorderColor }};">
                    {{ $riskCount > 0 ? $riskCount . ' Batch Berisiko' : 'Semua Aman' }}
                </span>
            </div>

            <!-- Big Centered Donut Chart -->
            <div class="ml-donut-wrapper-center">
                <svg class="ml-donut-svg" viewBox="0 0 220 220" width="200" height="200" role="img" aria-label="Grafik persentase risiko stok obat">
                    <circle cx="110" cy="110" r="80" class="ml-donut-track" />
                    <g transform="rotate(-90 110 110)">
                        @foreach($segments as $seg)
                            @php
                                $dashLen = $hasMultiple ? max(2, $seg['len'] - 4) : $seg['len'];
                            @endphp
                            <circle
                                cx="110"
                                cy="110"
                                r="80"
                                fill="none"
                                stroke="{{ $seg['color'] }}"
                                stroke-width="18"
                                stroke-dasharray="{{ number_format($dashLen, 2, '.', '') }} {{ number_format($circumference, 2, '.', '') }}"
                                stroke-dashoffset="{{ number_format(-$seg['offset'], 2, '.', '') }}"
                                stroke-linecap="butt"
                                class="ml-donut-segment"
                            >
                                <title>{{ $seg['label'] }}: {{ $seg['count'] }} batch ({{ $seg['pct'] }}%)</title>
                            </circle>
                        @endforeach
                    </g>
                    <!-- Center dynamic label -->
                    @if($riskCount > 0)
                        <text x="110" y="103" text-anchor="middle" class="ml-donut-center-val" fill="{{ $statusThemeColor }}">{{ $atRiskPercent }}%</text>
                        <text x="110" y="123" text-anchor="middle" class="ml-donut-center-lbl" fill="{{ $statusThemeColor }}">{{ $statusTitle }}</text>
                        <text x="110" y="139" text-anchor="middle" class="ml-donut-center-sub">{{ $riskCount }} dari {{ $totalBatches }} batch</text>
                    @else
                        <text x="110" y="103" text-anchor="middle" class="ml-donut-center-val" fill="#16A34A">100%</text>
                        <text x="110" y="123" text-anchor="middle" class="ml-donut-center-lbl" fill="#16A34A">Kondisi Aman</text>
                        <text x="110" y="139" text-anchor="middle" class="ml-donut-center-sub">Seluruh batch aman</text>
                    @endif
                </svg>
            </div>

            <!-- Slim Compact Legend below Big Donut -->
            <div class="ml-donut-legend-slim">
                <div class="ml-legend-row {{ $cCount > 0 ? '' : 'is-muted' }}">
                    <div class="ml-legend-left">
                        <span class="ml-legend-dot" style="background: #DC2626;"></span>
                        <span class="ml-legend-label">Kritis</span>
                        <span class="ml-legend-sub">(&le;30 hari)</span>
                    </div>
                    <div class="ml-legend-right">
                        <span class="ml-legend-count">{{ $cCount }} batch</span>
                        <span class="ml-legend-pct" style="color: #DC2626;">{{ $criticalPercent }}%</span>
                    </div>
                </div>

                <div class="ml-legend-row {{ $hCount > 0 ? '' : 'is-muted' }}">
                    <div class="ml-legend-left">
                        <span class="ml-legend-dot" style="background: #EA580C;"></span>
                        <span class="ml-legend-label">Tinggi</span>
                        <span class="ml-legend-sub">(&le;60 hari)</span>
                    </div>
                    <div class="ml-legend-right">
                        <span class="ml-legend-count">{{ $hCount }} batch</span>
                        <span class="ml-legend-pct" style="color: #EA580C;">{{ $highPercent }}%</span>
                    </div>
                </div>

                <div class="ml-legend-row {{ $mCount > 0 ? '' : 'is-muted' }}">
                    <div class="ml-legend-left">
                        <span class="ml-legend-dot" style="background: #D97706;"></span>
                        <span class="ml-legend-label">Sedang</span>
                        <span class="ml-legend-sub">(&le;90 hari)</span>
                    </div>
                    <div class="ml-legend-right">
                        <span class="ml-legend-count">{{ $mCount }} batch</span>
                        <span class="ml-legend-pct" style="color: #D97706;">{{ $mediumPercent }}%</span>
                    </div>
                </div>

                <div class="ml-legend-row {{ $lCount > 0 ? '' : 'is-muted' }}">
                    <div class="ml-legend-left">
                        <span class="ml-legend-dot" style="background: #16A34A;"></span>
                        <span class="ml-legend-label">Aman</span>
                        <span class="ml-legend-sub">(&gt;90 hari)</span>
                    </div>
                    <div class="ml-legend-right">
                        <span class="ml-legend-count">{{ $lCount }} batch</span>
                        <span class="ml-legend-pct" style="color: #16A34A;">{{ $lowPercent }}%</span>
                    </div>
                </div>
            </div>

            <div class="ml-stats-footer">
                <p class="ml-stats-footer-tip" style="border-left: 3px solid {{ $statusThemeColor }};">
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

    @if(isset($selectedBatch) && count($batchRecommendations) > 0)
        <section class="ml-section ml-rec-band" id="match-section" aria-labelledby="match-title">
            <div class="ml-section-header">
                <div>
                    <h2 class="ml-section-title" id="match-title">Rekomendasi Redistribusi Obat</h2>
                    <p class="ml-section-sub">
                        Sistem mencocokkan faskes penerima yang memiliki <strong>kebutuhan riil obat yang sama</strong>, <strong>jarak tempuh terdekat</strong>, dan <strong>kesiapan logistik/rantai dingin</strong>.
                    </p>
                </div>
            </div>

            <!-- Interactive Medicine Selector Dropdown -->
            <div class="ml-rec-selector-wrap">
                <label for="rec-batch-select" class="ml-rec-selector-label">
                    PILIH OBAT BERISIKO UNTUK MELIHAT FASKES YANG MEMBUTUHKAN:
                </label>
                <div class="ml-rec-select-box">
                    <select id="rec-batch-select" class="ml-rec-select" onchange="selectRecBatch(this.value)" aria-label="Pilih obat berisiko untuk melihat faskes yang membutuhkan">
                        @foreach($batchRecommendations as $bId => $recData)
                            @php
                                $b = $recData['batch'];
                                $isSelected = ($b->id === $selectedBatch->id);
                            @endphp
                            <option value="{{ $b->id }}" @selected($isSelected)>
                                {{ $b->medicine->name }} — Batch {{ $b->batch_number }} ({{ $b->risk_level }})
                            </option>
                        @endforeach
                    </select>
                    <svg class="ml-rec-select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </div>

            <!-- Batch Recommendation Panels -->
            @foreach($batchRecommendations as $bId => $recData)
                @php
                    $curBatch = $recData['batch'];
                    $curCandidates = $recData['candidates'];
                    $isCurActive = ($curBatch->id === $selectedBatch->id);
                @endphp
                <div class="ml-rec-batch-panel" id="rec-panel-{{ $bId }}" style="{{ $isCurActive ? '' : 'display: none;' }}">
                    <div class="ml-rec-panel-header">
                        <div class="ml-rec-panel-info">
                            <span class="ml-rec-panel-badge">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                                {{ $curBatch->medicine->name }}
                            </span>
                            <span class="ml-rec-panel-meta">Nomor Batch: <code>{{ $curBatch->batch_number }}</code> &middot; Stok Berisiko: {{ number_format($curBatch->projected_waste > 0 ? $curBatch->projected_waste : $curBatch->current_stock) }} {{ $curBatch->medicine->unit }}</span>
                        </div>
                        <span class="ml-rec-panel-formula">Skor Kecocokan = Kebutuhan Riil (50%) + Jarak (30%) + Logistik (20%)</span>
                    </div>

                    <div class="ml-rec-grid">
                        @forelse($curCandidates as $index => $candidate)
                            @php
                                $facilityType = match (strtoupper($candidate['facility']->type)) {
                                    'RSUD' => 'RSUD',
                                    'PUSKESMAS' => 'Puskesmas',
                                    'KLINIK' => 'Klinik',
                                    'DINKES' => 'Dinkes',
                                    default => ucwords(strtolower(str_replace('_', ' ', $candidate['facility']->type))),
                                };
                                $cap = (int) ($candidate['recipient_intake_cap'] ?? 0);
                                $priorityLabel = match($index) {
                                    0 => 'Prioritas Utama',
                                    1 => 'Prioritas Kedua',
                                    default => 'Alternatif',
                                };
                            @endphp
                            <article class="ml-rec-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <!-- Header Obat pada Kartu -->
                                    <div class="ml-rec-card-header">
                                        <span class="ml-rec-card-med">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                                            {{ $curBatch->medicine->name }}
                                        </span>
                                        <span class="ml-rec-card-batch">Batch {{ $curBatch->batch_number }}</span>
                                    </div>

                                    <div class="ml-rec-top">
                                        <span class="ml-rec-rank" aria-hidden="true">{{ $index + 1 }}</span>
                                        <div class="ml-rec-body">
                                            <div class="ml-rec-name">{{ $candidate['facility']->name }}</div>
                                            <div class="ml-rec-meta">
                                                {{ $facilityType }} &middot; {{ $candidate['distance_km'] }} km dari faskes Anda
                                            </div>
                                        </div>
                                        <div class="ml-rec-score-wrapper">
                                            <span class="ml-rec-score" title="Skor = Kebutuhan (50%) + Jarak (30%) + Logistik (20%)">
                                                {{ $candidate['match_score'] }}% Cocok
                                            </span>
                                            <span class="ml-rec-priority-tag is-p{{ $index + 1 }}">{{ $priorityLabel }}</span>
                                        </div>
                                    </div>

                                    <!-- Transparansi Perhitungan Skor -->
                                    <div class="ml-rec-calc-card">
                                        <div class="ml-rec-calc-title">
                                            <span>Rincian Penilaian Prioritas:</span>
                                            <span class="ml-rec-calc-total">Total: {{ $candidate['match_score'] }}%</span>
                                        </div>
                                        <div class="ml-rec-calc-list">
                                            <div class="ml-rec-calc-item">
                                                <span class="ml-rec-calc-key">1. Kebutuhan Riil (50%):</span>
                                                <span class="ml-rec-calc-val is-urgent">{{ $candidate['need_score'] }}%</span>
                                            </div>
                                            <div class="ml-rec-calc-sub">
                                                {{ $candidate['urgency_label'] }}
                                            </div>
                                            <div class="ml-rec-calc-item">
                                                <span class="ml-rec-calc-key">2. Jarak Terdekat (30%):</span>
                                                <span class="ml-rec-calc-val">{{ $candidate['proximity_score'] }}% ({{ $candidate['distance_km'] }} km)</span>
                                            </div>
                                            <div class="ml-rec-calc-item">
                                                <span class="ml-rec-calc-key">3. Kesiapan Logistik (20%):</span>
                                                <span class="ml-rec-calc-val">
                                                    {{ $candidate['logistics_score'] }}%
                                                    @if($curBatch->medicine->requires_cold_chain)
                                                        &middot; Cold-Chain Siap
                                                    @else
                                                        &middot; Suhu Ruang Siap
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-rec-facts" style="margin-top: 10px;">
                                        @if($cap > 0)
                                            <div>
                                                <div class="ml-rec-cap-line">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                                    <span>Kapasitas serap: <strong>{{ number_format($cap) }} {{ $curBatch->medicine->unit }}</strong></span>
                                                </div>
                                                <span class="ml-rec-cap-sub">Faskes ini dapat menghabiskan stok sebelum kedaluwarsa berdasarkan laju konsumsi.</span>
                                            </div>
                                        @else
                                            <span class="ml-rec-cap-generic">Faskes siap menerima alokasi transfer</span>
                                        @endif
                                    </div>
                                </div>

                                @if(! $user->isDinkesAuditor())
                                    <a href="{{ route('transfers.propose', ['batch' => $curBatch, 'recipient_id' => $candidate['facility']->id]) }}" class="ml-btn-primary ml-btn-sm ml-rec-cta" style="margin-top: 14px;">
                                        <span>Ajukan transfer ke faskes ini</span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                    </a>
                                @endif
                            </article>
                        @empty
                            <div class="ml-rec-empty">
                                Belum ada faskes penerima terdekat yang memenuhi syarat penyimpanan dan kebutuhan untuk batch ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach

            <p class="ml-rec-note">
                Rekomendasi bersifat saran sistem pendukung keputusan (DSS). Setiap transfer tetap memerlukan persetujuan Kepala Instalasi Farmasi faskes pengirim dan penerima.
            </p>
        </section>
    @endif
</div>

<script>
function selectRecBatch(batchId) {
    batchId = String(batchId);

    // 1. Hide all recommendation panels, show target
    document.querySelectorAll('.ml-rec-batch-panel').forEach(function(panel) {
        panel.style.display = 'none';
    });
    var targetPanel = document.getElementById('rec-panel-' + batchId);
    if (targetPanel) {
        targetPanel.style.display = 'block';
    }

    // 2. Synchronize dropdown selector
    var selectEl = document.getElementById('rec-batch-select');
    if (selectEl && selectEl.value !== batchId) {
        selectEl.value = batchId;
    }

    // 3. Highlight the clicked row in the overview table
    document.querySelectorAll('.ml-table-dashboard tbody tr').forEach(function(row) {
        row.classList.remove('is-selected-batch');
    });
    var targetRow = document.getElementById('dashboard-batch-row-' + batchId);
    if (targetRow) {
        targetRow.classList.add('is-selected-batch');
    }

    // 4. Update browser URL without reloading
    if (window.history && window.history.replaceState) {
        var url = new URL(window.location);
        url.searchParams.set('batch_id', batchId);
        window.history.replaceState({}, '', url);
    }
}
</script>
@endsection
