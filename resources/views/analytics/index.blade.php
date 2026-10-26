@extends('layouts.mediloop', ['title' => __('analytics.page_title')])

@section('content')
@php
    use App\Support\Formatter;

    $kpis = $analytics['kpis'];
    $riskCounts = $analytics['riskCounts'];
    $expiryBuckets = $analytics['expiryBuckets'];
    $categories = array_slice($analytics['categories'], 0, 6);
    $hasBatches = $kpis['batch_count'] > 0;

    $riskLegend = [
        ['key' => 'critical', 'label' => __('analytics.risk.critical'), 'count' => $riskCounts['CRITICAL']],
        ['key' => 'high', 'label' => __('analytics.risk.high'), 'count' => $riskCounts['HIGH']],
        ['key' => 'medium', 'label' => __('analytics.risk.medium'), 'count' => $riskCounts['MEDIUM']],
        ['key' => 'low', 'label' => __('analytics.risk.low'), 'count' => $riskCounts['LOW']],
    ];

    $chartData = [
        'risk' => [
            'labels' => [
                __('analytics.risk.critical_with_code'),
                __('analytics.risk.high_with_code'),
                __('analytics.risk.medium_with_code'),
                __('analytics.risk.low_with_code'),
            ],
            'values' => array_values($riskCounts),
        ],
        'category' => [
            'labels' => array_map(fn (array $category): string => (string) $category['name'], $categories),
            'values' => array_map(fn (array $category): float => (float) $category['waste_value'], $categories),
        ],
        'horizon' => [
            'labels' => [
                __('analytics.horizon.under_30'),
                __('analytics.horizon.days_31_60'),
                __('analytics.horizon.days_61_90'),
                __('analytics.horizon.over_90'),
            ],
            'values' => array_values($expiryBuckets),
        ],
        'labels' => [
            'waste_value' => __('analytics.charts.waste_value'),
            'batches' => __('analytics.charts.batches'),
            'batch' => __('analytics.charts.batch'),
        ],
    ];
@endphp

<div class="ml-analytics">
    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">{{ __('analytics.eyebrow') }}</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">{{ __('analytics.title') }}</h1>
                <span class="ml-facility-chip">
                    {{ $facility?->name ?? __('analytics.all_facilities') }}
                </span>
            </div>
            <p class="ml-page-sub">{{ __('analytics.subtitle') }}</p>
        </div>

        @if($isRegional)
            <form method="GET" action="{{ route('analytics.index') }}" class="ml-analytics-filter">
                <label for="facility_id" class="ml-analytics-filter-label">{{ __('analytics.filter_label') }}</label>
                <select name="facility_id" id="facility_id" class="ml-analytics-select">
                    <option value="">{{ __('analytics.all_facilities') }}</option>
                    @foreach($facilitiesList as $facilityOption)
                        <option value="{{ $facilityOption->id }}" @selected((int) request('facility_id') === $facilityOption->id)>
                            {{ $facilityOption->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="ml-btn-quiet">{{ __('analytics.apply_filter') }}</button>
            </form>
        @endif
    </header>

    <div class="ml-analytics-kpis">
        <article class="ml-analytics-kpi">
            <span class="ml-analytics-kpi-label">{{ __('analytics.kpi.inventory_label') }}</span>
            <span class="ml-analytics-kpi-value">{{ Formatter::idr($kpis['inventory_value']) }}</span>
            <div class="ml-analytics-kpi-meta">
                <span>{{ trans_choice('analytics.kpi.inventory_units', $kpis['stock_units'], ['count' => Formatter::number($kpis['stock_units'])]) }}</span>
                <strong>{{ trans_choice('analytics.kpi.inventory_batches', $kpis['batch_count'], ['count' => Formatter::number($kpis['batch_count'])]) }}</strong>
            </div>
        </article>

        <article class="ml-analytics-kpi is-risk">
            <span class="ml-analytics-kpi-label">{{ __('analytics.kpi.waste_label') }}</span>
            <span class="ml-analytics-kpi-value is-risk">{{ Formatter::idr($kpis['waste_value']) }}</span>
            <div class="ml-analytics-kpi-meta">
                <span>{{ trans_choice('analytics.kpi.waste_units', $kpis['waste_units'], ['count' => Formatter::number($kpis['waste_units'])]) }}</span>
                <strong class="is-risk">{{ str_replace(':ratio', Formatter::percent($kpis['waste_ratio']), __('analytics.kpi.waste_ratio')) }}</strong>
            </div>
        </article>

        <article class="ml-analytics-kpi is-efficiency">
            <span class="ml-analytics-kpi-label">{{ __('analytics.kpi.efficiency_label') }}</span>
            <span class="ml-analytics-kpi-value">{{ Formatter::percent($kpis['efficiency_score']) }}</span>
            <div class="ml-analytics-progress" role="img" aria-label="{{ Formatter::percent($kpis['efficiency_score']) }}">
                <span style="width: {{ max(0, min(100, $kpis['efficiency_score'])) }}%"></span>
            </div>
            <div class="ml-analytics-kpi-meta">
                <span>{{ __('analytics.kpi.efficiency_note') }}</span>
            </div>
        </article>

        <article class="ml-analytics-kpi is-primary">
            <span class="ml-analytics-kpi-label">{{ __('analytics.kpi.saved_label') }}</span>
            <span class="ml-analytics-kpi-value is-primary">{{ Formatter::idr($kpis['saved_value']) }}</span>
            <div class="ml-analytics-kpi-meta">
                <span>{{ trans_choice('analytics.kpi.saved_units', $kpis['saved_units'], ['count' => Formatter::number($kpis['saved_units'])]) }}</span>
                <strong class="is-primary">{{ __('analytics.kpi.saved_status') }}</strong>
            </div>
        </article>
    </div>

    @if(! $hasBatches)
        <section class="ml-analytics-empty" aria-labelledby="analytics-empty-title">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M3 3v18h18" />
                <path d="M7 15l4-4 3 3 5-6" />
            </svg>
            <div>
                <h2 id="analytics-empty-title">{{ __('analytics.empty.title') }}</h2>
                <p>{{ __('analytics.empty.description') }}</p>
            </div>
            @if(! $isRegional)
                <a href="{{ route('batches.create') }}" class="ml-btn-primary">{{ __('analytics.empty.cta') }}</a>
            @endif
        </section>
    @else
        <div class="ml-analytics-panels">
            <section class="ml-card ml-analytics-panel is-five" aria-labelledby="risk-chart-title">
                <div class="ml-analytics-panel-head">
                    <div>
                        <h2 class="ml-analytics-panel-title" id="risk-chart-title">{{ __('analytics.risk.title') }}</h2>
                        <p class="ml-analytics-panel-sub">{{ __('analytics.risk.subtitle') }}</p>
                    </div>
                    <span class="ml-analytics-panel-meta">
                        {{ trans_choice('analytics.risk.batch_count', $kpis['batch_count'], ['count' => Formatter::number($kpis['batch_count'])]) }}
                    </span>
                </div>

                <div class="ml-analytics-chart">
                    <canvas id="riskDonutChart" role="img" aria-label="{{ __('analytics.charts.risk_aria') }}"></canvas>
                </div>

                <ul class="ml-analytics-legend">
                    @foreach($riskLegend as $item)
                        <li>
                            <span class="ml-analytics-dot is-{{ $item['key'] }}" aria-hidden="true"></span>
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ Formatter::number($item['count']) }}</strong>
                        </li>
                    @endforeach
                </ul>

                <table class="sr-only">
                    <caption>{{ __('analytics.risk.title') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('analytics.risk.title') }}</th>
                            <th scope="col">{{ __('analytics.units.batches') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riskLegend as $item)
                            <tr>
                                <th scope="row">{{ $item['label'] }}</th>
                                <td>{{ Formatter::number($item['count']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="ml-card ml-analytics-panel is-seven" aria-labelledby="category-chart-title">
                <div class="ml-analytics-panel-head">
                    <div>
                        <h2 class="ml-analytics-panel-title" id="category-chart-title">{{ __('analytics.category.title') }}</h2>
                        <p class="ml-analytics-panel-sub">{{ __('analytics.category.subtitle') }}</p>
                    </div>
                    <span class="ml-analytics-panel-meta">{{ __('analytics.currency') }}</span>
                </div>

                <div class="ml-analytics-chart is-tall">
                    <canvas id="categoryWasteChart" role="img" aria-label="{{ __('analytics.charts.category_aria') }}"></canvas>
                </div>

                <table class="sr-only">
                    <caption>{{ __('analytics.category.title') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('analytics.category.title') }}</th>
                            <th scope="col">{{ __('analytics.charts.waste_value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <th scope="row">{{ $category['name'] }}</th>
                                <td>{{ Formatter::idr($category['waste_value']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="ml-card ml-analytics-panel is-five" aria-labelledby="horizon-chart-title">
                <div class="ml-analytics-panel-head">
                    <div>
                        <h2 class="ml-analytics-panel-title" id="horizon-chart-title">{{ __('analytics.horizon.title') }}</h2>
                        <p class="ml-analytics-panel-sub">{{ __('analytics.horizon.subtitle') }}</p>
                    </div>
                </div>

                <div class="ml-analytics-chart">
                    <canvas id="expiryHorizonChart" role="img" aria-label="{{ __('analytics.charts.horizon_aria') }}"></canvas>
                </div>

                <table class="sr-only">
                    <caption>{{ __('analytics.horizon.title') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('analytics.horizon.title') }}</th>
                            <th scope="col">{{ __('analytics.units.batches') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiryBuckets as $bucketKey => $bucketCount)
                            <tr>
                                <th scope="row">{{ __('analytics.horizon.'.$bucketKey) }}</th>
                                <td>{{ Formatter::number($bucketCount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="ml-card ml-analytics-panel is-seven" aria-labelledby="surplus-title">
                <div class="ml-analytics-panel-head">
                    <div>
                        <h2 class="ml-analytics-panel-title" id="surplus-title">{{ __('analytics.surplus.title') }}</h2>
                        <p class="ml-analytics-panel-sub">{{ __('analytics.surplus.subtitle') }}</p>
                    </div>
                    <a href="{{ route('batches.index') }}" class="ml-analytics-inline-link">
                        {{ __('analytics.surplus.manage_stock') }}
                    </a>
                </div>

                <div class="ml-analytics-table-wrap">
                    <table class="ml-analytics-table">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('analytics.surplus.medicine_batch') }}</th>
                                <th scope="col">{{ __('analytics.surplus.stock_burn') }}</th>
                                <th scope="col">{{ __('analytics.surplus.deplete_vs_expiry') }}</th>
                                <th scope="col">{{ __('analytics.surplus.surplus_ratio') }}</th>
                                <th scope="col" class="is-numeric">{{ __('analytics.surplus.potential_waste') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($analytics['surplus'] as $item)
                                <tr>
                                    <td>
                                        <span class="ml-analytics-medicine">{{ $item['medicine_name'] }}</span>
                                        <span class="ml-analytics-code">{{ $item['batch_number'] }}</span>
                                    </td>
                                    <td>
                                        <span class="ml-analytics-table-value">{{ Formatter::number($item['current_stock']) }} {{ __('analytics.units.units') }}</span>
                                        <span class="ml-analytics-table-sub">{{ Formatter::number($item['daily_burn_rate'], 1) }} {{ __('analytics.units.per_day') }}</span>
                                    </td>
                                    <td>
                                        <span class="ml-analytics-table-value">{{ str_replace(':days', Formatter::number($item['days_to_deplete'], 1), __('analytics.surplus.days_to_deplete')) }}</span>
                                        <span class="ml-analytics-table-sub is-risk">{{ str_replace(':days', Formatter::number($item['days_to_expiry']), __('analytics.surplus.days_to_expiry')) }}</span>
                                    </td>
                                    <td>
                                        <span class="ml-analytics-ratio is-{{ $item['surplus_ratio'] >= 2 ? 'critical' : 'high' }}">
                                            {{ str_replace(':ratio', Formatter::number($item['surplus_ratio'], 1), __('analytics.surplus.ratio')) }}
                                        </span>
                                    </td>
                                    <td class="is-numeric">
                                        <span class="ml-analytics-table-value">{{ Formatter::number($item['projected_waste']) }} {{ __('analytics.units.units') }}</span>
                                        <span class="ml-analytics-table-sub">{{ Formatter::idr($item['waste_value']) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="ml-analytics-table-empty">{{ __('analytics.surplus.empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="ml-card ml-analytics-panel" aria-labelledby="bar-chart-title">
                <div class="ml-analytics-panel-head">
                    <div>
                        <h2 class="ml-analytics-panel-title" id="bar-chart-title">{{ __('analytics.bar_chart.title') }}</h2>
                        <p class="ml-analytics-panel-sub">{{ __('analytics.bar_chart.subtitle') }}</p>
                    </div>
                </div>
                <div class="ml-analytics-chart is-tall">
                    <canvas id="barChart" role="img" aria-label="{{ __('analytics.bar_chart.aria') }}"></canvas>
                </div>
            </section>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script type="application/json" id="analytics-data">@json($chartData)</script>
@vite('resources/js/analytics.js')
@endsection

@push('styles')
<style>
    .ml-analytics { display: flex; flex-direction: column; gap: 18px; }

    .ml-analytics-filter {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 8px;
    }
    .ml-analytics-filter-label {
        width: 100%;
        color: var(--ml-muted);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .ml-analytics-select {
        min-height: 36px;
        max-width: 260px;
        padding: 7px 10px;
        border: 1px solid var(--ml-border);
        border-radius: 8px;
        background: var(--ml-card);
        color: var(--ml-text);
        font-family: var(--font-body);
        font-size: 12.5px;
    }
    .ml-analytics-select:focus-visible {
        outline: 2px solid var(--ml-primary);
        outline-offset: 2px;
    }

    .ml-analytics-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }
    .ml-analytics-kpi {
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        padding: 18px 18px 16px;
        overflow: hidden;
        border: 1px solid var(--ml-border);
        border-radius: 14px;
        background: var(--ml-card);
        box-shadow: var(--ml-shadow-panel);
    }
    .ml-analytics-kpi-label {
        color: var(--ml-muted);
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.07em;
        line-height: 1.4;
        text-transform: uppercase;
    }
    .ml-analytics-kpi-value {
        margin-top: 8px;
        color: var(--ml-text);
        font-family: var(--font-heading);
        font-size: clamp(22px, 2.1vw, 28px);
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.15;
        font-variant-numeric: tabular-nums;
        overflow-wrap: break-word;
    }
    .ml-analytics-kpi-value.is-risk { color: var(--color-risk-critical); }
    .ml-analytics-kpi-value.is-primary { color: var(--ml-primary); }
    .ml-analytics-kpi-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 4px 10px;
        margin-top: 10px;
        color: var(--ml-muted);
        font-size: 11px;
        line-height: 1.45;
    }
    .ml-analytics-kpi-meta strong { font-weight: 700; }
    .ml-analytics-kpi-meta strong.is-risk { color: var(--color-risk-critical); }
    .ml-analytics-kpi-meta strong.is-primary { color: var(--ml-primary); }
    .ml-analytics-progress {
        height: 7px;
        margin-top: 10px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--ml-surface);
    }
    .ml-analytics-progress span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--ml-primary);
    }

    .ml-analytics-panels {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        grid-template-rows: 1fr;
        gap: 18px;
        align-items: start;
    }
    .ml-analytics-panel {
        grid-column: span 12;
        min-width: 0;
        padding: 18px 20px 20px;
        border-radius: 14px;
    }
    .ml-analytics-panel.is-five { grid-column: span 5; }
    .ml-analytics-panel.is-seven { grid-column: span 7; }
    .ml-analytics-panel-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--ml-border);
    }
    .ml-analytics-panel-title {
        margin: 0;
        color: var(--ml-text);
        font-family: var(--font-heading);
        font-size: 14px;
        font-weight: 700;
        letter-spacing: -0.015em;
        line-height: 1.35;
    }
    .ml-analytics-panel-sub {
        margin: 4px 0 0;
        color: var(--ml-muted);
        font-size: 11.5px;
        line-height: 1.5;
        max-width: 58ch;
    }
    .ml-analytics-panel-meta {
        flex-shrink: 0;
        padding: 4px 8px;
        border: 1px solid var(--ml-border);
        border-radius: 6px;
        background: var(--ml-surface);
        color: var(--ml-muted);
        font-family: var(--font-code);
        font-size: 10.5px;
        font-weight: 600;
        white-space: nowrap;
    }
    .ml-analytics-chart {
        position: relative;
        height: 238px;
        padding: 14px 0 4px;
    }
    .ml-analytics-chart.is-tall { height: 268px; }
    .ml-analytics-chart canvas { width: 100% !important; height: 100% !important; }

    .ml-analytics-legend {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 14px;
        margin: 0;
        padding: 14px 0 0;
        border-top: 1px solid var(--ml-border);
        list-style: none;
    }
    .ml-analytics-legend li {
        display: flex;
        align-items: center;
        gap: 7px;
        color: var(--ml-muted);
        font-size: 11.5px;
        line-height: 1.4;
    }
    .ml-analytics-legend strong {
        margin-left: auto;
        color: var(--ml-text);
        font-family: var(--font-code);
        font-size: 11px;
        font-weight: 600;
    }
    .ml-analytics-dot {
        width: 9px;
        height: 9px;
        flex-shrink: 0;
        border-radius: 999px;
        background: var(--color-risk-low);
    }
    .ml-analytics-dot.is-critical { background: var(--color-risk-critical); }
    .ml-analytics-dot.is-high { background: var(--color-risk-high); }
    .ml-analytics-dot.is-medium { background: var(--color-risk-medium); }
    .ml-analytics-dot.is-low { background: var(--color-risk-low); }

    .ml-analytics-inline-link {
        flex-shrink: 0;
        color: var(--ml-primary);
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
    }
    .ml-analytics-inline-link:hover { text-decoration: underline; }
    .ml-analytics-inline-link:focus-visible {
        outline: 2px solid var(--ml-primary);
        outline-offset: 3px;
        border-radius: 4px;
    }

    .ml-analytics-table-wrap {
        margin-top: 4px;
        overflow-x: auto;
    }
    .ml-analytics-table {
        width: 100%;
        min-width: 680px;
        border-collapse: collapse;
        font-size: 11.5px;
    }
    .ml-analytics-table th,
    .ml-analytics-table td {
        padding: 11px 10px;
        border-bottom: 1px solid var(--ml-border);
        text-align: left;
        vertical-align: top;
    }
    .ml-analytics-table th:first-child,
    .ml-analytics-table td:first-child { padding-left: 0; }
    .ml-analytics-table th:last-child,
    .ml-analytics-table td:last-child { padding-right: 0; }
    .ml-analytics-table thead th {
        color: var(--ml-muted);
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.03em;
        line-height: 1.35;
        text-transform: uppercase;
    }
    .ml-analytics-table tbody tr:last-child td { border-bottom: 0; }
    .ml-analytics-table .is-numeric { text-align: right; }
    .ml-analytics-medicine {
        display: block;
        color: var(--ml-text);
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.4;
    }
    .ml-analytics-code {
        display: block;
        margin-top: 3px;
        color: var(--ml-subtle);
        font-family: var(--font-code);
        font-size: 10.5px;
        line-height: 1.4;
        overflow-wrap: break-word;
    }
    .ml-analytics-table-value {
        display: block;
        color: var(--ml-text);
        font-weight: 600;
        line-height: 1.4;
        font-variant-numeric: tabular-nums;
    }
    .ml-analytics-table-sub {
        display: block;
        margin-top: 3px;
        color: var(--ml-muted);
        font-size: 10.5px;
        line-height: 1.4;
        font-variant-numeric: tabular-nums;
    }
    .ml-analytics-table-sub.is-risk { color: var(--color-risk-critical); font-weight: 600; }
    .ml-analytics-ratio {
        display: inline-block;
        padding: 3px 7px;
        border: 1px solid var(--ml-border);
        border-radius: 6px;
        background: var(--ml-surface);
        color: var(--ml-text);
        font-family: var(--font-code);
        font-size: 10.5px;
        font-weight: 600;
        white-space: nowrap;
    }
    .ml-analytics-ratio.is-critical {
        border-color: var(--color-risk-critical);
        background: var(--color-feedback-danger-surface);
        color: var(--color-feedback-danger-ink);
    }
    .ml-analytics-ratio.is-high {
        border-color: var(--color-risk-high);
        background: var(--color-warning-surface, var(--ml-surface));
        color: var(--color-risk-high);
    }
    .ml-analytics-table-empty {
        padding: 24px 0 !important;
        color: var(--ml-muted);
        text-align: center !important;
    }

    .ml-analytics-empty {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        padding: 20px;
        border: 1px dashed var(--ml-border);
        border-radius: 14px;
        background: var(--ml-card);
        color: var(--ml-muted);
    }
    .ml-analytics-empty svg { flex-shrink: 0; color: var(--ml-subtle); }
    .ml-analytics-empty h2 {
        margin: 0;
        color: var(--ml-text);
        font-size: 13.5px;
        font-weight: 700;
    }
    .ml-analytics-empty p {
        max-width: 64ch;
        margin: 4px 0 0;
        font-size: 12px;
        line-height: 1.55;
    }
    .ml-analytics-empty .ml-btn-primary { margin-left: auto; }

    @media (max-width: 1100px) {
        .ml-analytics-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ml-analytics-panel.is-five,
        .ml-analytics-panel.is-seven { grid-column: span 12; }
    }
    @media (max-width: 720px) {
        .ml-analytics-kpis { grid-template-columns: 1fr; }
        .ml-analytics-filter { width: 100%; }
        .ml-analytics-select { max-width: none; flex: 1; }
        .ml-analytics-panel { padding: 16px; }
        .ml-analytics-chart { height: 220px; }
        .ml-analytics-chart.is-tall { height: 240px; }
        .ml-analytics-empty .ml-btn-primary { width: 100%; justify-content: center; margin-left: 0; }
    }
</style>
@endpush
