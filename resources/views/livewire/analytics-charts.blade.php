<div class="ml-analytics-charts" wire:poll.1s="refresh">
    <script type="application/json" id="analytics-data-livewire">@json($chartData)</script>

    @if(!empty($chartData['risk']['values']))
        <section class="ml-card ml-analytics-panel is-five" aria-labelledby="risk-chart-title">
            <div class="ml-analytics-panel-head">
                <div>
                    <h2 class="ml-analytics-panel-title" id="risk-chart-title">{{ __('analytics.risk.title') }}</h2>
                    <p class="ml-analytics-panel-sub">{{ __('analytics.risk.subtitle') }}</p>
                </div>
            </div>
            <div class="ml-analytics-chart">
                <canvas id="riskDonutChart" role="img" aria-label="{{ __('analytics.risk.aria') }}"></canvas>
            </div>
        </section>
    @endif

    @if(!empty($chartData['category']['values']))
        <section class="ml-card ml-analytics-panel" aria-labelledby="category-chart-title">
            <div class="ml-analytics-panel-head">
                <div>
                    <h2 class="ml-analytics-panel-title" id="category-chart-title">{{ __('analytics.category_chart.title') }}</h2>
                    <p class="ml-analytics-panel-sub">{{ __('analytics.category_chart.subtitle') }}</p>
                </div>
            </div>
            <div class="ml-analytics-chart">
                <canvas id="categoryWasteChart" role="img" aria-label="{{ __('analytics.category_chart.aria') }}"></canvas>
            </div>
        </section>
    @endif

    @if(!empty($chartData['horizon']['values']))
        <section class="ml-card ml-analytics-panel" aria-labelledby="horizon-chart-title">
            <div class="ml-analytics-panel-head">
                <div>
                    <h2 class="ml-analytics-panel-title" id="horizon-chart-title">{{ __('analytics.horizon.title') }}</h2>
                    <p class="ml-analytics-panel-sub">{{ __('analytics.horizon.subtitle') }}</p>
                </div>
            </div>
            <div class="ml-analytics-chart">
                <canvas id="expiryHorizonChart" role="img" aria-label="{{ __('analytics.horizon.aria') }}"></canvas>
            </div>
        </section>
    @endif

    @if(!empty($chartData['category']['values']))
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
    @endif
</div>
