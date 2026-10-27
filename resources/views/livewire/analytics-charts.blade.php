<div class="ml-analytics-panels" wire:poll.60s="refresh">
    <script type="application/json" id="analytics-data-livewire">@json($chartData)</script>

    @if(!empty($chartData['risk']['values']))
        <section class="ml-card ml-analytics-panel is-five" aria-labelledby="risk-chart-title">
            <div class="ml-analytics-panel-head">
                <div>
                    <h2 class="ml-analytics-panel-title" id="risk-chart-title">{{ __('analytics.risk.title') }}</h2>
                    <p class="ml-analytics-panel-sub">{{ __('analytics.risk.subtitle') }}</p>
                </div>
            </div>
            <div class="ml-analytics-chart" wire:ignore>
                <canvas id="riskDonutChart" role="img" aria-label="{{ __('analytics.risk.aria') }}"></canvas>
            </div>
            <ul class="ml-analytics-legend">
                <li>
                    <span class="ml-analytics-dot is-critical" aria-hidden="true"></span>
                    <span>{{ __('analytics.risk.critical_with_code') }}</span>
                    <strong>{{ number_format($chartData['risk']['values'][0] ?? 0, 0, ',', '.') }}</strong>
                </li>
                <li>
                    <span class="ml-analytics-dot is-high" aria-hidden="true"></span>
                    <span>{{ __('analytics.risk.high_with_code') }}</span>
                    <strong>{{ number_format($chartData['risk']['values'][1] ?? 0, 0, ',', '.') }}</strong>
                </li>
                <li>
                    <span class="ml-analytics-dot is-medium" aria-hidden="true"></span>
                    <span>{{ __('analytics.risk.medium_with_code') }}</span>
                    <strong>{{ number_format($chartData['risk']['values'][2] ?? 0, 0, ',', '.') }}</strong>
                </li>
                <li>
                    <span class="ml-analytics-dot is-low" aria-hidden="true"></span>
                    <span>{{ __('analytics.risk.low_with_code') }}</span>
                    <strong>{{ number_format($chartData['risk']['values'][3] ?? 0, 0, ',', '.') }}</strong>
                </li>
            </ul>
        </section>
    @endif

    @if(!empty($chartData['category']['values']))
        <section class="ml-card ml-analytics-panel is-seven" aria-labelledby="category-chart-title">
            <div class="ml-analytics-panel-head">
                <div>
                    <h2 class="ml-analytics-panel-title" id="category-chart-title">{{ __('analytics.category.title') }}</h2>
                    <p class="ml-analytics-panel-sub">{{ __('analytics.category.subtitle') }}</p>
                </div>
            </div>
            <div class="ml-analytics-chart" wire:ignore>
                <canvas id="categoryWasteChart" role="img" aria-label="{{ __('analytics.charts.category_aria') }}"></canvas>
            </div>
        </section>
    @endif

    @if(!empty($chartData['horizon']['values']))
        <section class="ml-card ml-analytics-panel is-five" aria-labelledby="horizon-chart-title">
            <div class="ml-analytics-panel-head">
                <div>
                    <h2 class="ml-analytics-panel-title" id="horizon-chart-title">{{ __('analytics.horizon.title') }}</h2>
                    <p class="ml-analytics-panel-sub">{{ __('analytics.horizon.subtitle') }}</p>
                </div>
            </div>
            <div class="ml-analytics-chart" wire:ignore>
                <canvas id="expiryHorizonChart" role="img" aria-label="{{ __('analytics.horizon.aria') }}"></canvas>
            </div>
        </section>
    @endif

    @if(!empty($chartData['surplus']['items']))
        <section class="ml-card ml-analytics-panel is-seven" aria-labelledby="surplus-table-title">
            <div class="ml-analytics-panel-head">
                <div>
                    <h2 class="ml-analytics-panel-title" id="surplus-table-title">{{ __('analytics.surplus.title') }}</h2>
                    <p class="ml-analytics-panel-sub">{{ __('analytics.surplus.subtitle') }}</p>
                </div>
                <a href="{{ route('analytics.index') }}" class="ml-analytics-inline-link">{{ __('analytics.surplus.manage_stock') }}</a>
            </div>
            <div class="ml-analytics-table-wrap">
                <table class="ml-analytics-table">
                    <thead>
                        <tr>
                            <th>{{ __('analytics.surplus.medicine_batch') }}</th>
                            <th class="is-numeric">{{ __('analytics.surplus.stock_burn') }}</th>
                            <th>{{ __('analytics.surplus.deplete_vs_expiry') }}</th>
                            <th class="is-numeric">{{ __('analytics.surplus.surplus_ratio') }}</th>
                            <th class="is-numeric">{{ __('analytics.surplus.potential_waste') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chartData['surplus']['items'] as $item)
                        <tr>
                            <td>
                                <span class="ml-analytics-medicine">{{ $item['medicine_name'] ?? '-' }}</span>
                                <span class="ml-analytics-code">{{ $item['batch_number'] ?? '-' }}</span>
                            </td>
                            <td class="is-numeric">
                                <span class="ml-analytics-table-value">{{ $item['current_stock'] ?? 0 }} unit</span>
                                <span class="ml-analytics-table-sub">{{ ($item['daily_burn_rate'] ?? 0) > 0 ? round($item['daily_burn_rate'], 1) . ' unit/hari' : '-' }}</span>
                            </td>
                            <td>
                                <span class="ml-analytics-table-value">{{ ($item['days_to_deplete'] ?? 0) > 0 ? round($item['days_to_deplete'], 1) . ' hari habis' : '-' }}</span>
                                <span class="ml-analytics-table-sub is-risk">{{ ($item['days_to_expiry'] ?? 0) > 0 ? round($item['days_to_expiry'], 1) . ' hari exp' : '-' }}</span>
                            </td>
                            <td class="is-numeric">
                                <span class="ml-analytics-ratio {{ ($item['surplus_ratio'] ?? 0) > 2 ? 'is-critical' : (($item['surplus_ratio'] ?? 0) > 1 ? 'is-high' : '') }}">{{ ($item['surplus_ratio'] ?? 0) > 0 ? $item['surplus_ratio'] . 'x Surplus' : '-' }}</span>
                            </td>
                            <td class="is-numeric">
                                <span class="ml-analytics-table-value is-risk">{{ ($item['waste_value'] ?? 0) > 0 ? 'Rp ' . number_format($item['waste_value'], 0, ',', '.') : 'Rp 0' }}</span>
                                <span class="ml-analytics-table-sub">{{ ($item['projected_waste'] ?? 0) > 0 ? $item['projected_waste'] . ' unit' : '-' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
