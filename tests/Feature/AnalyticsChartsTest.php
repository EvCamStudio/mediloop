<?php

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('analytics charts keep a stable canvas that livewire re-renders must not touch', function () {
    $facility = Facility::create([
        'name' => 'RSUD Tarakan Jakarta',
        'code' => 'FSK-ANALYTICS',
        'type' => 'RSUD',
        'address' => 'Jl. Kyai Caringin No. 7',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1729000,
        'longitude' => 106.8122000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Hendra Wijaya',
        'phone' => '021-3842934',
    ]);

    $user = User::factory()->create([
        'facility_id' => $facility->id,
        'role' => 'FACILITY_STAFF',
    ]);

    $medicine = Medicine::create([
        'name' => 'Ceftriaxone 1g Injeksi',
        'generic_name' => 'Ceftriaxone Sodium',
        'sku' => 'MED-CFT-ANALYTICS',
        'category' => 'Antibiotic',
        'dosage_form' => 'Vial Injeksi',
        'unit' => 'Vial',
        'unit_price_idr' => 45000,
    ]);

    Batch::create([
        'facility_id' => $facility->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'CFT-ANALYTICS-01',
        'current_stock' => 480,
        'daily_burn_rate' => 4.0,
        'expiry_date' => Carbon::today()->addDays(45)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    $response = $this->actingAs($user)->get(route('analytics.index'));

    $response->assertOk();
    $response->assertSee('id="riskDonutChart"', false);
    $response->assertSee('id="categoryWasteChart"', false);
    $response->assertSee('id="expiryHorizonChart"', false);
    $response->assertSee('analytics-data-livewire', false);

    // The chart wrappers opt out of Livewire morphing so Chart.js instances and
    // their canvases survive the wire:poll refresh.
    expect(substr_count($response->getContent(), 'wire:ignore'))->toBe(3);
});
