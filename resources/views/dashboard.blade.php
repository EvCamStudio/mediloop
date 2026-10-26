@extends('layouts.mediloop', ['title' => __('dashboard.page_title')])

@section('content')
@php
    $batchCollection = $batches ?? collect();
    $highRiskCount = $batchCollection->filter(fn ($batch) => in_array($batch->risk_level, ['HIGH', 'CRITICAL'], true))->count();
    $watchRiskCount = max(0, ($atRiskCount ?? 0) - $highRiskCount);
    $totalUnits = $batchCollection->sum('current_stock');
    $networkCount = isset($facilitiesList) ? $facilitiesList->count() : 0;

    $cCount = $criticalCount ?? $batchCollection->where('risk_level', 'CRITICAL')->count();
    $hCount = $highCount ?? $batchCollection->where('risk_level', 'HIGH')->count();
    $mCount = $mediumCount ?? $batchCollection->where('risk_level', 'MEDIUM')->count();
    $lCount = $lowCount ?? $batchCollection->where('risk_level', 'LOW')->count();
    $riskCount = $atRiskCount ?? ($cCount + $hCount + $mCount);

    $totalBatches = $batchCollection->count();
    $atRiskPercent = $totalBatches > 0 ? round(($riskCount / $totalBatches) * 100) : 0;

    // SVG Donut calculations
    $circumference = 502.655;
    $segments = [];
    $offset = 0;
    $colors = ['CRITICAL' => '#DC2626', 'HIGH' => '#EA580C', 'MEDIUM' => '#D97706', 'LOW' => '#16A34A'];
    $labels = ['CRITICAL' => 'Kritis', 'HIGH' => 'Tinggi', 'MEDIUM' => 'Sedang', 'LOW' => 'Aman'];
    
    foreach (['CRITICAL', 'HIGH', 'MEDIUM', 'LOW'] as $level) {
        $count = $batchCollection->where('risk_level', $level)->count();
        if ($count > 0) {
            $len = ($count / $totalBatches) * $circumference;
            $segments[] = [
                'color' => $colors[$level],
                'len' => $len,
                'offset' => $offset,
                'label' => $labels[$level],
                'count' => $count,
                'pct' => round(($count / $totalBatches) * 100),
            ];
            $offset += $len;
        }
    }

    // Status Theme from fix_zoe
    if ($cCount > 0) {
        $statusThemeColor = '#DC2626'; $statusBgColor = '#FEF2F2'; $statusBorderColor = '#FECACA'; $statusTextColor = '#991B1B';
        $statusTitle = 'Risiko Kritis'; $statusSub = $cCount . ' batch kritis mendesak';
    } elseif ($hCount > 0) {
        $statusThemeColor = '#EA580C'; $statusBgColor = '#FFF7ED'; $statusBorderColor = '#FED7AA'; $statusTextColor = '#C2410C';
        $statusTitle = 'Risiko Tinggi'; $statusSub = $hCount . ' batch risiko tinggi';
    } elseif ($mCount > 0) {
        $statusThemeColor = '#D97706'; $statusBgColor = '#FFFBEB'; $statusBorderColor = '#FDE68A'; $statusTextColor = '#B45309';
        $statusTitle = 'Risiko Sedang'; $statusSub = $mCount . ' batch risiko sedang';
    } else {
        $statusThemeColor = '#16A34A'; $statusBgColor = '#F0FDF4'; $statusBorderColor = '#BBF7D0'; $statusTextColor = '#15803D';
        $statusTitle = 'Kondisi Aman'; $statusSub = 'Seluruh batch aman';
    }
@endphp

<div class="ml-dashboard">
    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">{{ __('dashboard.eyebrow') }}</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">{{ __('dashboard.title') }}</h1>
                <span class="ml-facility-chip">{{ $facility?->name ?? __('dashboard.facility_fallback') }}</span>
            </div>
            <p class="ml-page-sub">{{ __('dashboard.subtitle') }}</p>
        </div>

        @if (! ($facilityMissing ?? false))
            <div class="ml-page-actions">
                <a href="{{ route('batches.index') }}" class="ml-btn-quiet">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                    <span>{{ __('dashboard.manage_stock') }}</span>
                </a>
                @if(! $user->isDinkesAuditor())
                    <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v16m8-8H4"/></svg>
                        <span>{{ __('dashboard.input_batch') }}</span>
                    </a>
                @endif
            </div>
        @endif
    </header>

    @if ($facilityMissing ?? false)
        <div class="ml-alert ml-alert-warning" role="status">
            <strong style="display: block; margin-bottom: 4px;">Akun belum terhubung ke fasilitas</strong>
            <p style="font-size: 0.875rem; opacity: 0.9;">Data stok antar fasilitas tidak ditampilkan sampai akun Anda dihubungkan ke fasilitas terverifikasi.</p>
        </div>
    @endif

    <div class="ml-kpi-grid">
        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">{{ __('dashboard.kpi.at_risk') }}</span>
                <span class="ml-kpi-icon is-risk" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01" /><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($riskCount, 0, ',', '.') }} <span style="font-size: 0.875rem; opacity: 0.7;">batch</span></div>
            <div class="ml-kpi-meta">
                @if($riskCount > 0)
                    <span class="ml-kpi-emphasis">{{ $cCount }} kritis</span>
                    <span class="ml-meta-dot">·</span> {{ $hCount }} tinggi
                    <span class="ml-meta-dot">·</span> {{ $mCount }} sedang
                @else
                    <span>Seluruh batch aman</span>
                @endif
            </div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">{{ __('dashboard.kpi.active_stock') }}</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($totalUnits, 0, ',', '.') }} <span style="font-size: 0.875rem; opacity: 0.7;">unit</span></div>
            <div class="ml-kpi-meta">{{ number_format($totalBatches, 0, ',', '.') }} batch aktif tercatat</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">{{ __('dashboard.kpi.risk_value') }}</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value ml-kpi-value-currency">Rp {{ number_format($totalProjectedWasteValue ?? 0, 0, ',', '.') }}</div>
            <div class="ml-kpi-meta">{{ __('dashboard.kpi.risk_value_meta') }}</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">{{ __('dashboard.kpi.network') }}</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13A4 4 0 0116 11" /></svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($networkCount, 0, ',', '.') }} <span style="font-size: 0.875rem; opacity: 0.7;">faskes</span></div>
            <div class="ml-kpi-meta">{{ __('dashboard.kpi.network_meta') }}</div>
        </article>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 18px; margin-top: 18px;">
        <!-- LEFT: PREDICTION TABLE -->
        <section class="ml-section" aria-labelledby="forecast-title">
            <div class="ml-section-header ml-section-header-split">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div>
                        <h2 class="ml-section-title" id="forecast-title">{{ __('dashboard.forecast.title') }}</h2>
                        <p class="ml-section-sub">{{ __('dashboard.forecast.subtitle') }}</p>
                    </div>
                </div>
                <a href="{{ route('batches.index') }}" class="ml-section-link">{{ __('dashboard.forecast.view_all') }}</a>
            </div>
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy">
                    <caption class="ml-sr-only">{{ __('dashboard.forecast.table_caption') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dashboard.forecast.columns.medicine_batch') }}</th>
                            <th scope="col" class="is-right">{{ __('dashboard.forecast.columns.physical_stock') }}</th>
                            <th scope="col">{{ __('dashboard.forecast.columns.shelf_life') }}</th>
                            <th scope="col" class="is-right">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batchCollection->take(3) as $batch)
                            @php
                                $daysToDeplete = is_finite($batch->days_to_deplete) ? max(0, (float) $batch->days_to_deplete) : null;
                                $daysToExpiry = max(1, (int) $batch->days_to_expiry);
                                $timelineDays = max($daysToExpiry, $daysToDeplete ?? $daysToExpiry);
                                $depletePercent = $daysToDeplete === null ? 100 : min(100, round($daysToDeplete / $timelineDays * 100, 1));
                                $expiryPercent = min(100, round(max(0, $daysToExpiry) / $timelineDays * 100, 1));
                                $isLowRisk = $batch->risk_level === 'LOW';
                                $riskLabel = match ($batch->risk_level) {
                                    'CRITICAL' => __('enums.risk.critical'),
                                    'HIGH' => __('enums.risk.high'),
                                    'MEDIUM' => __('enums.risk.medium'),
                                    'LOW' => __('enums.risk.low'),
                                    default => ucfirst(strtolower((string) $batch->risk_level)),
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="ml-cell-title">{{ $batch->medicine->name }}</div>
                                    <div class="ml-cell-meta">
                                        {{ $batch->medicine->dosage_form }} &middot; {{ $batch->medicine->category }}
                                        @if($batch->medicine->requires_cold_chain)
                                            <span class="ml-cold-chain" role="img" title="{{ __('dashboard.forecast.cold_chain') }}" aria-label="{{ __('dashboard.forecast.cold_chain') }}">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9L4.9 19.1"/></svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-cell-meta ml-cell-mono">
                                        {{ __('dashboard.forecast.batch_expiry', ['batch' => $batch->batch_number, 'date' => $batch->expiry_date->format('d M Y')]) }}
                                    </div>
                                </td>
                                <td class="ml-cell-stock">
                                    <span class="ml-stock-value">{{ number_format($batch->current_stock) }}</span>
                                    <span class="ml-stock-unit">{{ $batch->medicine->unit }}</span>
                                </td>
                                <td>
                                    <div class="ml-runway-text">
                                        <span class="{{ $isLowRisk ? 'is-safe' : 'is-risk' }}">
                                            {{ __('dashboard.forecast.expires_in', ['days' => $batch->days_to_expiry]) }}
                                        </span>
                                        <span class="ml-runway-sep" aria-hidden="true">&middot;</span>
                                        <span>
                                            {{ $daysToDeplete === null ? __('dashboard.forecast.stock_never') : __('dashboard.forecast.stock_out_in', ['days' => number_format($daysToDeplete, 0)]) }}
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
                                                <span>{{ __('dashboard.forecast.find_recipient') }}</span>
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                            </a>
                                        @else
                                            <a href="{{ route('batches.edit', $batch) }}" class="ml-btn-quiet ml-btn-sm">
                                                {{ __('common.view_detail') }}
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
                                        <div class="ml-empty-title">{{ __('dashboard.forecast.empty_title') }}</div>
                                        <p class="ml-empty-copy">{{ __('dashboard.forecast.empty_copy') }}</p>
                                        @if(! $user->isDinkesAuditor())
                                            <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                                                {{ __('dashboard.forecast.empty_cta') }}
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

        <!-- RIGHT: MODERN STATS & DONUT CHART -->
        <aside class="ml-card" style="padding: 20px; border-radius: 14px; border: 1px solid var(--ml-border); background: var(--ml-card);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <h2 class="ml-section-title" style="font-size: 1.1rem; margin: 0;">Komposisi Risiko Stok</h2>
                    <p class="ml-section-sub" style="font-size: 0.75rem; margin: 4px 0 0;">Total {{ $totalBatches }} batch aktif</p>
                </div>
                <span style="background: {{ $statusBgColor }}; color: {{ $statusTextColor }}; border: 1px solid {{ $statusBorderColor }}; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">
                    {{ $riskCount > 0 ? $riskCount . ' Batch Berisiko' : 'Semua Aman' }}
                </span>
            </div>

            <div style="text-align: center; position: relative; margin-bottom: 20px;">
                <svg class="ml-donut-svg" viewBox="0 0 220 220" width="200" height="200" role="img" aria-label="Grafik persentase risiko stok obat" style="display: block; margin: 0 auto;">
                    <circle cx="110" cy="110" r="80" fill="transparent" stroke="var(--ml-border)" stroke-width="18" />
                    @foreach($segments as $seg)
                        <circle cx="110" cy="110" r="80" fill="transparent" 
                                stroke="{{ $seg['color'] }}" 
                                stroke-width="18" 
                                stroke-dasharray="{{ $seg['len'] }} {{ $circumference - $seg['len'] }}" 
                                stroke-dashoffset="{{ $seg['offset'] }}" 
                                stroke-linecap="round" />
                    @endforeach
                    
                    @if($riskCount > 0)
                        <text x="110" y="105" text-anchor="middle" fill="{{ $statusThemeColor }}" style="font-family: var(--font-heading); font-weight: 800; font-size: 34px; line-height: 1;">{{ $atRiskPercent }}%</text>
                        <text x="110" y="125" text-anchor="middle" fill="{{ $statusThemeColor }}" style="font-weight: 700; font-size: 0.85rem;">{{ $statusTitle }}</text>
                        <text x="110" y="140" text-anchor="middle" fill="var(--ml-muted)" style="font-size: 0.7rem;">{{ $riskCount }} dari {{ $totalBatches }} batch</text>
                    @else
                        <text x="110" y="105" text-anchor="middle" fill="#16A34A" style="font-family: var(--font-heading); font-weight: 800; font-size: 34px; line-height: 1;">100%</text>
                        <text x="110" y="125" text-anchor="middle" fill="#16A34A" style="font-weight: 700; font-size: 0.85rem;">Kondisi Aman</text>
                        <text x="110" y="140" text-anchor="middle" fill="var(--ml-muted)" style="font-size: 0.7rem;">Seluruh batch aman</text>
                    @endif
                </svg>
            </div>

            <div class="ml-donut-legend" style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($segments as $seg)
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--ml-text); {{ $seg['count'] == 0 ? 'opacity: 0.4;' : '' }}">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $seg['color'] }};"></span>
                            <span>{{ $seg['label'] }}</span>
                        </div>
                        <div style="font-weight: 600;">{{ $seg['count'] }} ({{ $seg['pct'] }}%)</div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 20px; padding-top: 12px; border-top: 1px solid var(--ml-border); font-size: 0.7rem; color: var(--ml-muted); line-height: 1.4; font-style: italic; border-left: 3px solid {{ $statusThemeColor }}; padding-left: 8px;">
                @if($cCount > 0) Prioritas utama: Terdapat <strong>{{ $cCount }} batch kritis</strong> yang mendesak untuk ditransfer segera.
                @elseif($hCount > 0) Disarankan: Terdapat <strong>{{ $hCount }} batch risiko tinggi</strong> yang dapat ditawarkan ke faskes sekitar.
                @elseif($mCount > 0) Dalam pemantauan: Batch risiko sedang terpantau stabil.
                @else Kondisi prima: Seluruh batch obat berada dalam pemakaian aman terkendali.
                @endif
            </div>
        </aside>
    </div>

    @if(isset($topRiskBatch))
        <livewire:dashboard-recommendations :initialBatchId="$topRiskBatch->id" />
    @endif
</div>
@endsection