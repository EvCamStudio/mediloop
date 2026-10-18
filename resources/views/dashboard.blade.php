@extends('layouts.mediloop', ['title' => 'Overview Dashboard - MediLoop'])

@section('content')
<div>

    {{-- ============================================================ --}}
    {{-- 4 STAT CARDS --}}
    {{-- ============================================================ --}}
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

    {{-- ============================================================ --}}
    {{-- TWO-COLUMN: Table + Map Panel --}}
    {{-- ============================================================ --}}
    <div class="ml-two-col">

        {{-- LEFT: Forecast Table --}}
        <div class="ml-section">
            <div class="ml-section-header">
                <h2 class="ml-section-title">At-Risk Expiry Forecast &amp; Match Recommendation</h2>
            </div>
            <table class="ml-table">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Batch #</th>
                        <th>Stock</th>
                        <th>Days to Deplete vs Expiry</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches ?? [] as $batch)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--ml-text); font-size: 13.5px;">
                                    {{ $batch->medicine->name ?? 'Amoxicillin 500mg' }}
                                </div>
                                @if(isset($batch->medicine->dosage_form))
                                    <div style="font-size: 11px; color: var(--ml-muted); margin-top: 2px;">
                                        {{ $batch->medicine->dosage_form }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="font-family: monospace; font-weight: 600; color: var(--ml-text);">
                                    {{ $batch->batch_number ?? 'AMX-09' }}
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--ml-muted); font-size: 12.5px;">
                                    Stock:<br>
                                    <span style="font-weight: 700; color: var(--ml-text); font-size: 13.5px;">
                                        {{ number_format($batch->current_stock ?? 480) }} boxes
                                    </span>
                                </span>
                            </td>
                            <td>
                                <div class="ml-days-wrap">
                                    <span class="ml-days-pill ml-days-expiry">
                                        {{ $batch->days_to_expiry ?? 45 }} Days<br>
                                        <span style="font-size: 9.5px; font-weight:400;">to Expiry</span>
                                    </span>
                                    <span class="ml-days-vs">vs</span>
                                    <span class="ml-days-pill ml-days-deplete">
                                        {{ $batch->days_to_deplete ?? 120 }} Days<br>
                                        <span style="font-size: 9.5px; font-weight:400;">to Deplete</span>
                                    </span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    @php $risk = $batch->risk_level ?? 'HIGH'; @endphp
                                    @if($risk === 'HIGH' || $risk === 'CRITICAL')
                                        <span class="ml-risk-high">HIGH<br>RISK</span>
                                    @elseif($risk === 'MEDIUM')
                                        <span class="ml-risk-medium">MEDIUM<br>RISK</span>
                                    @endif
                                    <a href="#" class="ml-btn-primary">Find Match</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Demo rows when no data --}}
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--ml-text); font-size: 13.5px;">Amoxicillin 500mg</div>
                                <div style="font-size: 11px; color: var(--ml-muted); margin-top: 2px;">Tablet</div>
                            </td>
                            <td><span style="font-family: monospace; font-weight: 600; color: var(--ml-text);">AMX-09</span></td>
                            <td>
                                <span style="color: var(--ml-muted); font-size: 12px;">Stock:<br>
                                    <span style="font-weight: 700; color: var(--ml-text);">480 boxes</span>
                                </span>
                            </td>
                            <td>
                                <div class="ml-days-wrap">
                                    <span class="ml-days-pill ml-days-expiry">45 Days<br><span style="font-size: 9.5px; font-weight:400;">to Expiry</span></span>
                                    <span class="ml-days-vs">vs</span>
                                    <span class="ml-days-pill ml-days-deplete">120 Days<br><span style="font-size: 9.5px; font-weight:400;">to Deplete</span></span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    <span class="ml-risk-high" style="text-align:center; line-height:1.2;">HIGH<br>RISK</span>
                                    <a href="#" class="ml-btn-primary">Find Match</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--ml-text); font-size: 13.5px;">Paracetamol Syrup</div>
                                <div style="font-size: 11px; color: var(--ml-muted); margin-top: 2px;">Syrup</div>
                            </td>
                            <td><span style="font-family: monospace; font-weight: 600; color: var(--ml-text);">PCT-44</span></td>
                            <td>
                                <span style="color: var(--ml-muted); font-size: 12px;">Stock:<br>
                                    <span style="font-weight: 700; color: var(--ml-text);">120 boxes</span>
                                </span>
                            </td>
                            <td>
                                <div class="ml-days-wrap">
                                    <span class="ml-days-pill ml-days-expiry">30 Days<br><span style="font-size: 9.5px; font-weight:400;">to Expiry</span></span>
                                    <span class="ml-days-vs">vs</span>
                                    <span class="ml-days-pill ml-days-deplete">40 Days<br><span style="font-size: 9.5px; font-weight:400;">to Deplete</span></span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    <span class="ml-risk-medium" style="text-align:center; line-height:1.2;">MEDIUM<br>RISK</span>
                                    <a href="#" class="ml-btn-primary">Find Match</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- RIGHT: Map + Match Suggestions --}}
        <div style="display: flex; flex-direction: column; gap: 16px;">

            {{-- Network Map --}}
            <div class="ml-section">
                <div class="ml-section-header">
                    <h3 class="ml-section-title">Interactive Network Map</h3>
                </div>
                <div class="ml-map-container">
                    {{-- Simulated map background --}}
                    <svg width="100%" height="100%" viewBox="0 0 340 200" xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;">
                        {{-- Road lines simulating map --}}
                        <rect width="340" height="200" fill="#E8EDF5"/>
                        {{-- Streets --}}
                        <line x1="0" y1="80" x2="340" y2="80" stroke="#D1DCF0" stroke-width="6"/>
                        <line x1="0" y1="130" x2="340" y2="130" stroke="#D1DCF0" stroke-width="4"/>
                        <line x1="80" y1="0" x2="80" y2="200" stroke="#D1DCF0" stroke-width="4"/>
                        <line x1="200" y1="0" x2="200" y2="200" stroke="#D1DCF0" stroke-width="3"/>
                        <line x1="260" y1="0" x2="260" y2="200" stroke="#D1DCF0" stroke-width="2"/>
                        {{-- District blocks --}}
                        <rect x="90" y="90" width="100" height="30" fill="#D8E5F2" rx="2"/>
                        <rect x="210" y="100" width="40" height="20" fill="#D8E5F2" rx="2"/>
                        {{-- Dashed route line --}}
                        <line x1="68" y1="68" x2="270" y2="140" stroke="#93C5FD" stroke-width="2" stroke-dasharray="6 4"/>
                        {{-- Distance labels --}}
                        <text x="50" y="60" font-size="9" fill="#6B84B0" font-family="DM Sans, sans-serif">4.0 km</text>
                        <text x="130" y="118" font-size="9" fill="#6B84B0" font-family="DM Sans, sans-serif">4.6 km</text>
                        <text x="190" y="165" font-size="9" fill="#6B84B0" font-family="DM Sans, sans-serif">2.6 km</text>
                        <text x="240" y="180" font-size="9" fill="#6B84B0" font-family="DM Sans, sans-serif">4.0 km</text>
                    </svg>

                    {{-- Source facility pin --}}
                    <div class="ml-map-pin" style="top: 48px; left: 44px;">
                        <div class="ml-pin-dot" style="background: var(--ml-orange);"></div>
                        <div class="ml-pin-label">
                            Puskesmas Simpang
                            <small>Puskesmas Simpang</small>
                            <span style="display:inline-block; margin-top:1px; background: var(--ml-orange); color:#fff; font-size:8px; font-weight:700; padding:1px 5px; border-radius:4px;">RISK</span>
                        </div>
                    </div>

                    {{-- Destination facility pin --}}
                    <div class="ml-map-pin" style="bottom: 36px; right: 16px;">
                        <div class="ml-pin-dot" style="background: var(--ml-primary);"></div>
                        <div class="ml-pin-label">
                            Puskesmas Medan Baru
                            <small>Puskesmas Medan Baru</small>
                        </div>
                    </div>
                </div>

                {{-- Match Suggestions --}}
                <div style="padding: 0 16px 4px;">
                    <div style="font-size: 13.5px; font-weight: 700; color: var(--ml-text); font-family: var(--font-heading); padding-bottom: 10px; border-bottom: 1px solid var(--ml-border);">
                        Match Suggestions
                    </div>
                </div>
                <div class="ml-match-list">
                    <div class="ml-match-item">
                        <div class="ml-match-rank">1</div>
                        <div class="ml-match-info">
                            <div class="ml-match-name">Puskesmas Medan Baru</div>
                            <div class="ml-match-meta">Score 92% · Needs 200 boxes</div>
                        </div>
                        <a href="#" class="ml-btn-primary" style="font-size: 11.5px; padding: 6px 12px;">Create Transfer</a>
                    </div>
                    <div class="ml-match-item">
                        <div class="ml-match-rank">2</div>
                        <div class="ml-match-info">
                            <div class="ml-match-name">Klinik Sentosa</div>
                            <div class="ml-match-meta">Score 81% · Needs 100 boxes</div>
                        </div>
                        <a href="#" class="ml-btn-ghost" style="font-size: 11.5px; padding: 6px 12px;">Route Map</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


