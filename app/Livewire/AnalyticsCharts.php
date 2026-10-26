<?php

namespace App\Livewire;

use App\Models\Batch;
use App\Models\Transfer;
use App\Services\AnalyticsService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class AnalyticsCharts extends Component
{
    public ?int $facilityId = null;

    public bool $isRegional = false;

    public array $chartData = [];

    public function mount(?int $facilityId, bool $isRegional): void
    {
        $this->facilityId = $facilityId;
        $this->isRegional = $isRegional;
        $this->loadChartData();
    }

    public function loadChartData(): void
    {
        $batches = $this->scopedBatches();
        $completedTransfers = $this->completedTransfers();

        $analytics = (new AnalyticsService)->summarize($batches, $completedTransfers);

        $this->chartData = [
            'risk' => [
                'labels' => [
                    __('analytics.risk.critical_with_code'),
                    __('analytics.risk.high_with_code'),
                    __('analytics.risk.medium_with_code'),
                    __('analytics.risk.low_with_code'),
                ],
                'values' => array_values($analytics['riskCounts'] ?? []),
            ],
            'category' => [
                'labels' => array_map(fn (array $category): string => (string) $category['name'], $analytics['categories'] ?? []),
                'values' => array_map(fn (array $category): float => (float) $category['waste_value'], $analytics['categories'] ?? []),
            ],
            'horizon' => [
                'labels' => [
                    __('analytics.horizon.under_30'),
                    __('analytics.horizon.days_31_60'),
                    __('analytics.horizon.days_61_90'),
                    __('analytics.horizon.over_90'),
                ],
                'values' => array_values($analytics['expiryBuckets'] ?? []),
            ],
            'surplus' => [
                'items' => $analytics['surplus'] ?? [],
                'labels' => [
                    'medicine_name' => __('analytics.surplus.medicine_name'),
                    'batch_number' => __('analytics.surplus.batch_number'),
                    'current_stock' => __('analytics.surplus.current_stock'),
                    'days_to_expiry' => __('analytics.surplus.days_to_expiry'),
                    'surplus_ratio' => __('analytics.surplus.surplus_ratio'),
                    'projected_waste' => __('analytics.surplus.projected_waste'),
                ],
            ],
            'labels' => [
                'waste_value' => __('analytics.charts.waste_value'),
                'batches' => __('analytics.charts.batches'),
                'batch' => __('analytics.charts.batch'),
            ],
        ];
    }

    public function refresh(): void
    {
        $this->loadChartData();
    }

    private function scopedBatches(): Collection
    {
        if (! $this->isRegional && $this->facilityId === null) {
            return new Collection;
        }

        return Batch::with(['medicine', 'facility'])
            ->where('status', 'ACTIVE')
            ->when($this->facilityId !== null, fn ($query) => $query->where('facility_id', $this->facilityId))
            ->orderBy('expiry_date')
            ->get();
    }

    private function completedTransfers(): Collection
    {
        if (! $this->isRegional && $this->facilityId === null) {
            return new Collection;
        }

        return Transfer::with('batch')
            ->where('status', 'COMPLETED')
            ->when($this->facilityId !== null, function ($query): void {
                $query->where(function ($subQuery): void {
                    $subQuery->where('donor_facility_id', $this->facilityId)
                        ->orWhere('recipient_facility_id', $this->facilityId);
                });
            })
            ->get();
    }

    public function render()
    {
        return view('livewire.analytics-charts', [
            'chartData' => $this->chartData,
        ]);
    }
}
