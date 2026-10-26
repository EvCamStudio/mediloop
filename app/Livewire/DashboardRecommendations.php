<?php

namespace App\Livewire;

use App\Models\Batch;
use App\Services\RedistributionEngine;
use Illuminate\Support\Collection;
use Livewire\Component;

class DashboardRecommendations extends Component
{
    public ?int $selectedBatchId = null;

    public array $candidates = [];

    public ?Batch $currentBatch = null;

    // Change to protected/public property that can be accessed in view
    // In Livewire 3, public properties are automatically passed to the view
    public Collection $riskBatches;

    public function mount(?int $initialBatchId = null): void
    {
        $this->selectedBatchId = $initialBatchId;

        // Ensure we fetch batches connected to the user's facility
        // This ensures the dropdown is connected to the real DB and current facility
        $userFacilityId = auth()->user()?->facility_id;

        $batches = Batch::with('medicine')
            ->when($userFacilityId, fn ($q) => $q->where('facility_id', $userFacilityId))
            ->get();

        $this->riskBatches = $batches->filter(fn (Batch $b) => in_array($b->risk_level, ['CRITICAL', 'HIGH', 'MEDIUM']))
            ->sortByDesc('risk_level');

        $this->updateCandidates();
    }

    public function updatedSelectedBatchId($value): void
    {
        $this->selectedBatchId = $value ? (int) $value : null;
        $this->updateCandidates();
    }

    public function updateCandidates(): void
    {
        if (! $this->selectedBatchId) {
            $this->candidates = [];
            $this->currentBatch = null;

            return;
        }

        try {
            $batch = Batch::findOrFail($this->selectedBatchId);
            $this->currentBatch = $batch;

            $candidates = RedistributionEngine::findCandidateRecipients($batch);

            // Limit to top 3 candidates for dashboard summary
            $candidates = array_slice($candidates, 0, 3);

            // Add status label for transparency
            $this->candidates = array_map(function ($c) {
                $score = $c['match_score'];
                $status = 'Kebutuhan Terdeteksi';
                if ($score > 95) {
                    $status = 'Defisit Kritis';
                } elseif ($score > 90) {
                    $status = 'Stok Menipis';
                } elseif ($score > 70) {
                    $status = 'Kebutuhan Moderat';
                }

                $c['status_label'] = $status;

                return $c;
            }, $candidates);
        } catch (\Exception $e) {
            $this->candidates = [];
            $this->currentBatch = null;
        }
    }

    public function render()
    {
        return view('livewire.dashboard-recommendations', [
            'riskBatches' => $this->riskBatches,
        ]);
    }
}
