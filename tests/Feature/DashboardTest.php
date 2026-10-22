<?php

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('users without a facility do not see cross-facility inventory', function () {
    $facility = Facility::create([
        'name' => 'RSUD Tarakan Jakarta',
        'code' => 'FSK-RSUD-TRK',
        'type' => 'RSUD',
        'address' => 'Jl. Kyai Caringin No. 7',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1729000,
        'longitude' => 106.8122000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Hendra Wijaya',
        'phone' => '021-3842934',
    ]);

    $medicine = Medicine::create([
        'name' => 'Ceftriaxone 1g Injeksi',
        'generic_name' => 'Ceftriaxone Sodium',
        'sku' => 'MED-CFT-001',
        'category' => 'Antibiotic',
        'dosage_form' => 'Vial Injeksi',
        'unit' => 'Vial',
        'unit_price_idr' => 45000,
    ]);

    Batch::create([
        'facility_id' => $facility->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'CFT-2026-X89',
        'current_stock' => 480,
        'daily_burn_rate' => 4.0,
        'expiry_date' => now()->addDays(45)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    $user = User::factory()->create(['facility_id' => null]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Akun Belum Terhubung ke Fasilitas');
    $response->assertDontSee('Ceftriaxone 1g Injeksi');
    $response->assertDontSee('CFT-2026-X89');
});
