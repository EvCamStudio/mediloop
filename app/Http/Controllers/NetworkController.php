<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Services\RedistributionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NetworkController extends Controller
{
    /**
     * Display the facility network map with distance-ranked partners.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $origin = $user->facility_id ? Facility::find($user->facility_id) : null;

        $facilityTypes = Facility::query()
            ->where('type', '!=', 'DINKES')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        $selectedType = $request->string('type')->toString();
        $selectedType = $facilityTypes->contains($selectedType) ? $selectedType : null;
        $coldChainOnly = $request->boolean('cold_chain');

        $facilities = $this->filteredFacilities($origin, $selectedType, $coldChainOnly);

        $rows = $origin
            ? RedistributionEngine::nearestFacilities($origin, $facilities)
            : $this->rowsWithoutOrigin($facilities);

        return view('network.index', [
            'user' => $user,
            'origin' => $origin,
            'rows' => $rows,
            'facilityTypes' => $facilityTypes,
            'selectedType' => $selectedType,
            'coldChainOnly' => $coldChainOnly,
            'stats' => $this->stats($rows),
            'mapData' => $this->mapData($origin, $rows),
        ]);
    }

    /**
     * @return Collection<int, Facility>
     */
    private function filteredFacilities(?Facility $origin, ?string $type, bool $coldChainOnly): Collection
    {
        return Facility::query()
            ->where('type', '!=', 'DINKES')
            ->when($origin, fn ($query) => $query->whereKeyNot($origin->id))
            ->when($type, fn ($query, string $value) => $query->where('type', $value))
            ->when($coldChainOnly, fn ($query) => $query->where('cold_chain_ready', true))
            ->orderBy('name')
            ->get();
    }

    /**
     * Build rows for users whose account is not linked to a facility.
     *
     * @param  Collection<int, Facility>  $facilities
     * @return array<int, array{facility: Facility, distance_km: null, proximity_score: null}>
     */
    private function rowsWithoutOrigin(Collection $facilities): array
    {
        return $facilities
            ->map(fn (Facility $facility): array => [
                'facility' => $facility,
                'distance_km' => null,
                'proximity_score' => null,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{facilities: int, in_range: int, nearest_km: float|null}
     */
    private function stats(array $rows): array
    {
        $distances = [];

        foreach ($rows as $row) {
            if (is_float($row['distance_km'])) {
                $distances[] = $row['distance_km'];
            }
        }

        $inRange = array_filter(
            $distances,
            fn (float $distance): bool => $distance <= RedistributionEngine::PROXIMITY_MAX_RADIUS_KM
        );

        return [
            'facilities' => count($rows),
            'in_range' => count($inRange),
            'nearest_km' => $distances[0] ?? null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    private function mapData(?Facility $origin, array $rows): array
    {
        return [
            'origin' => $origin ? $this->facilityPayload($origin, null) : null,
            'maxRadiusKm' => RedistributionEngine::PROXIMITY_MAX_RADIUS_KM,
            'facilities' => array_map(
                fn (array $row): array => $this->facilityPayload(
                    $row['facility'],
                    is_float($row['distance_km']) ? $row['distance_km'] : null
                ),
                $rows
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function facilityPayload(Facility $facility, ?float $distanceKm): array
    {
        return [
            'id' => $facility->id,
            'name' => $facility->name,
            'type' => $facility->type,
            'address' => $facility->address,
            'city' => $facility->city,
            'lat' => (float) $facility->latitude,
            'lng' => (float) $facility->longitude,
            'distanceKm' => $distanceKm,
            'coldChainReady' => (bool) $facility->cold_chain_ready,
        ];
    }
}
