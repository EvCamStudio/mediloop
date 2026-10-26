<?php

namespace App\Livewire;

use App\Models\Batch;
use App\Models\Transfer;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
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

    private function scopedBatches(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->isRegional && $this->facilityId === null) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        return Batch::with(['medicine', 'facility'])
            ->where('status', 'ACTIVE')
            ->when($this->facilityId !== null, fn ($query) => $query->where('facility_id', $this->facilityId))
            ->orderBy('expiry_date')
            ->get();
    }

    private function completedTransfers(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->isRegional && $this->facilityId === null) {
            return new \Illuminate\Database\Eloquent\Collection;
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
