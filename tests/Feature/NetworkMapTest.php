<?php

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Models\User;
use App\Services\RedistributionEngine;

function createNetworkFacility(
    string $name,
    string $code,
    float $latitude,
    float $longitude,
    string $type = 'PUSKESMAS',
    bool $coldChainReady = true
): Facility {
    return Facility::create([
        'name' => $name,
        'code' => $code,
        'type' => $type,
        'address' => 'Jl. Pengujian No. 1',
        'city' => 'Jakarta Pusat',
        'latitude' => $latitude,
        'longitude' => $longitude,
        'cold_chain_ready' => $coldChainReady,
        'contact_person' => 'Apt. Penguji',
        'phone' => '021-0000000',
    ]);
}

test('network map requires authentication', function () {
    $this->get('/network')->assertRedirect('/login');
});

test('network page ranks partner facilities from nearest and excludes dinkes', function () {
    $origin = createNetworkFacility('RSUD Tarakan Jakarta', 'FSK-RSUD-TRK', -6.1729000, 106.8122000, 'RSUD');
    $near = createNetworkFacility('Puskesmas Kecamatan Gambir', 'FSK-PKM-GMB', -6.1764000, 106.8285000);
    $far = createNetworkFacility('Klinik Jakarta Selatan', 'FSK-KLN-JS', -6.2085000, 106.8145000, 'KLINIK');
    $dinkes = createNetworkFacility('Dinkes DKI Jakarta', 'FSK-DINKES-DKI', -6.1750000, 106.8200000, 'DINKES');

    $user = User::factory()->create([
        'facility_id' => $origin->id,
        'role' => 'FACILITY_STAFF',
    ]);

    $response = $this->actingAs($user)->get(route('network.index'));

    $response->assertOk()
        ->assertSee('Facility Network Map')
        ->assertSeeInOrder([$near->name, $far->name])
        ->assertDontSee($dinkes->name)
        ->assertSee('network-map', false);

    $mapData = $response->viewData('mapData');

    expect($mapData['origin']['id'])->toBe($origin->id)
        ->and($mapData['facilities'])->toHaveCount(2)
        ->and($mapData['facilities'][0]['id'])->toBe($near->id)
        ->and($mapData['facilities'][0]['distanceKm'])->toBeGreaterThan(0)
        ->and($mapData['facilities'][0]['distanceKm'])->toBeLessThan($mapData['facilities'][1]['distanceKm']);

    preg_match(
        '/<script type="application\/json" id="network-map-data">(.*?)<\/script>/s',
        $response->getContent(),
        $matches
    );
    $embeddedMapData = json_decode($matches[1] ?? '', true);

    expect($embeddedMapData)->toBeArray()
        ->and($embeddedMapData['origin']['id'])->toBe($origin->id)
        ->and((float) $embeddedMapData['maxRadiusKm'])->toBe(RedistributionEngine::PROXIMITY_MAX_RADIUS_KM);
});

test('network page filters facilities by type and cold chain readiness', function () {
    $origin = createNetworkFacility('RSUD Tarakan Jakarta', 'FSK-RSUD-TRK', -6.1729000, 106.8122000, 'RSUD');
    $coldChainClinic = createNetworkFacility('Klinik Rantai Dingin', 'FSK-KLN-COLD', -6.1764000, 106.8285000, 'KLINIK', true);
    $regularClinic = createNetworkFacility('Klinik Biasa', 'FSK-KLN-REG', -6.1900000, 106.8200000, 'KLINIK', false);

    $user = User::factory()->create([
        'facility_id' => $origin->id,
        'role' => 'FACILITY_STAFF',
    ]);

    $typeFiltered = $this->actingAs($user)->get(route('network.index', ['type' => 'KLINIK']));
    $typeFiltered->assertOk();
    expect($typeFiltered->viewData('rows'))->toHaveCount(2);

    $coldChainFiltered = $this->actingAs($user)
        ->get(route('network.index', ['type' => 'KLINIK', 'cold_chain' => 1]));
    $coldChainFiltered->assertOk();

    $rows = $coldChainFiltered->viewData('rows');

    expect($rows)->toHaveCount(1)
        ->and($rows[0]['facility']->id)->toBe($coldChainClinic->id)
        ->and($rows[0]['facility']->id)->not->toBe($regularClinic->id);
});

test('network page still renders for auditors without a linked facility', function () {
    createNetworkFacility('Puskesmas Kecamatan Gambir', 'FSK-PKM-GMB', -6.1764000, 106.8285000);
    createNetworkFacility('Klinik Jakarta Selatan', 'FSK-KLN-JS', -6.2085000, 106.8145000, 'KLINIK');

    $auditor = User::factory()->create([
        'facility_id' => null,
        'role' => 'DINKES_AUDITOR',
    ]);

    $response = $this->actingAs($auditor)->get(route('network.index'));

    $response->assertOk()
        ->assertSee('Distance unavailable');

    $rows = $response->viewData('rows');

    expect($response->viewData('mapData')['origin'])->toBeNull()
        ->and($rows)->toHaveCount(2)
        ->and($rows[0]['distance_km'])->toBeNull();
});

test('propose page can rank recipients by nearest distance', function () {
    $donor = createNetworkFacility('RSUD Tarakan Jakarta', 'FSK-RSUD-TRK', -6.1729000, 106.8122000, 'RSUD');
    $near = createNetworkFacility('Puskesmas Kecamatan Gambir', 'FSK-PKM-GMB', -6.1764000, 106.8285000);
    $far = createNetworkFacility('Klinik Jakarta Selatan', 'FSK-KLN-JS', -6.2085000, 106.8145000, 'KLINIK');

    $medicine = Medicine::create([
        'name' => 'Ceftriaxone 1g Injeksi',
        'generic_name' => 'Ceftriaxone Sodium',
        'sku' => 'MED-CFT-NET-001',
        'category' => 'Antibiotic',
        'dosage_form' => 'Vial Injeksi',
        'unit' => 'Vial',
        'unit_price_idr' => 45000,
        'requires_cold_chain' => false,
    ]);

    $batch = Batch::create([
        'facility_id' => $donor->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'CFT-2026-NET',
        'current_stock' => 480,
        'daily_burn_rate' => 4.0,
        'expiry_date' => now()->addDays(45)->toDateString(),
        'unit_price_idr' => 45000,
        'status' => 'ACTIVE',
    ]);

    $user = User::factory()->create([
        'facility_id' => $donor->id,
        'role' => 'FACILITY_STAFF',
    ]);

    $response = $this->actingAs($user)->get(route('transfers.propose', [
        'batch' => $batch->id,
        'sort' => 'distance',
    ]));

    $response->assertOk()->assertSee('Nearest');

    $candidates = $response->viewData('candidates');

    expect($response->viewData('sort'))->toBe('distance')
        ->and($candidates[0]['facility']->id)->toBe($near->id)
        ->and($candidates[1]['facility']->id)->toBe($far->id)
        ->and($candidates[0]['distance_km'])->toBeLessThanOrEqual($candidates[1]['distance_km']);

    $fallbackResponse = $this->actingAs($user)->get(route('transfers.propose', [
        'batch' => $batch->id,
        'sort' => 'unsupported',
    ]));

    expect($fallbackResponse->viewData('sort'))->toBe('score');
});
