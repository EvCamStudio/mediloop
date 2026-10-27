<?php

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public landing page renders with clinical decision-support content and simulator', function () {
    config(['app.locale' => 'id']);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('MediLoop');
    $response->assertSee('Sistem Pendukung Keputusan Hibah');
    $response->assertSee('Kalkulator Simulasi Prediksi Limbah');
    $response->assertSee('Ceftriaxone 1g Injeksi (480, 4.0, 45)');
});

test('public landing page exposes a main landmark and meta description', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('<main id="main-content"', false);
    $response->assertSee('<meta name="description"', false);
});

test('landing call-to-action sends guests to login and demo shortcut is removed', function () {
    config(['app.locale' => 'id']);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Buka Dashboard');
    $response->assertSee('Mulai Simulasi');
    $response->assertSee('href="'.route('login').'"', false);
    $response->assertDontSee('/demo/donor-staff', false);

    $this->get('/demo/donor-staff')->assertNotFound();
});

test('dashboard displays inventory ledger and risk badges', function () {
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

    $user = User::factory()->create([
        'facility_id' => $facility->id,
        'role' => 'FACILITY_STAFF',
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

    $batch = Batch::create([
        'facility_id' => $facility->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'CFT-2026-X89',
        'current_stock' => 480,
        'daily_burn_rate' => 4.0,
        'expiry_date' => Carbon::today()->addDays(45)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('CFT-2026-X89');
    $response->assertSee('Ceftriaxone 1g Injeksi');
    $response->assertSee('Tinggi');
    $response->assertSee('Rekomendasi');
});

test('complete transfer lifecycle and invariant verification', function () {
    $donor = Facility::create([
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

    $recipient = Facility::create([
        'name' => 'Puskesmas Kecamatan Gambir',
        'code' => 'FSK-PKM-GMB',
        'type' => 'PUSKESMAS',
        'address' => 'Jl. Tanah Abang II No. 27',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1764000,
        'longitude' => 106.8285000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Dewi Sartika',
        'phone' => '021-3501234',
    ]);

    $donorStaff = User::factory()->create(['facility_id' => $donor->id, 'role' => 'FACILITY_STAFF']);
    $donorApprover = User::factory()->create(['facility_id' => $donor->id, 'role' => 'FACILITY_APPROVER']);
    $recipientStaff = User::factory()->create(['facility_id' => $recipient->id, 'role' => 'FACILITY_STAFF']);
    $recipientApprover = User::factory()->create(['facility_id' => $recipient->id, 'role' => 'FACILITY_APPROVER']);

    $medicine = Medicine::create([
        'name' => 'Ceftriaxone 1g Injeksi',
        'generic_name' => 'Ceftriaxone Sodium',
        'sku' => 'MED-CFT-001',
        'category' => 'Antibiotic',
        'dosage_form' => 'Vial Injeksi',
        'unit' => 'Vial',
        'unit_price_idr' => 45000,
    ]);

    $batch = Batch::create([
        'facility_id' => $donor->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'CFT-2026-X89',
        'current_stock' => 480,
        'daily_burn_rate' => 4.0,
        'expiry_date' => Carbon::today()->addDays(45)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    // 1. Propose screen
    $proposeResponse = $this->actingAs($donorStaff)->get("/transfers/propose/{$batch->id}");
    $proposeResponse->assertStatus(200);
    $proposeResponse->assertSee('Puskesmas Kecamatan Gambir');

    // 2. Submit proposal
    $storeResponse = $this->actingAs($donorStaff)->post('/transfers', [
        'batch_id' => $batch->id,
        'recipient_facility_id' => $recipient->id,
        'quantity' => 100,
        'notes' => 'Redistribusi darurat kekurangan antibiotik.',
    ]);
    $storeResponse->assertRedirect();

    $transfer = Transfer::first();
    expect($transfer)->not->toBeNull()
        ->and($transfer->status)->toBe('PROPOSED')
        ->and($transfer->quantity)->toBe(100);

    // INVARIANT CHECK: Donor stock still 480
    expect($batch->fresh()->current_stock)->toBe(480);

    // 3. Donor Head approves
    $this->actingAs($donorApprover)->post("/transfers/{$transfer->id}/approve")->assertRedirect();
    expect($transfer->fresh()->status)->toBe('APPROVED');
    expect($batch->fresh()->current_stock)->toBe(480);

    // 4. Recipient accepts
    $this->actingAs($recipientStaff)->post("/transfers/{$transfer->id}/accept")->assertRedirect();
    expect($transfer->fresh()->status)->toBe('ACCEPTED');
    expect($batch->fresh()->current_stock)->toBe(480);

    // 5. Donor dispatches shipment
    $this->actingAs($donorStaff)->post("/transfers/{$transfer->id}/dispatch", [
        'courier_type' => 'Kurir Khusus Farmasi',
        'tracking_number' => 'RESI-TEST-99',
    ])->assertRedirect();
    expect($transfer->fresh()->status)->toBe('IN_TRANSIT');
    expect($batch->fresh()->current_stock)->toBe(480);

    // 6. Recipient Approver confirms physical arrival -> ATOMIC STOCK MUTATION
    $this->actingAs($recipientApprover)->post("/transfers/{$transfer->id}/complete")->assertRedirect();
    expect($transfer->fresh()->status)->toBe('COMPLETED');

    // INVARIANT VERIFIED: Donor stock reduced, Recipient stock created
    expect($batch->fresh()->current_stock)->toBe(380);
    $recipientBatch = Batch::where('facility_id', $recipient->id)->where('batch_number', 'CFT-2026-X89')->first();
    expect($recipientBatch)->not->toBeNull()
        ->and($recipientBatch->current_stock)->toBe(100);
});

test('audit oversight page renders for dinkes auditor', function () {
    $auditorFacility = Facility::create([
        'name' => 'Dinas Kesehatan DKI Jakarta',
        'code' => 'DKS-DKI',
        'type' => 'DINKES',
        'address' => 'Jl. Kesehatan No. 10',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1831000,
        'longitude' => 106.8315000,
        'cold_chain_ready' => true,
        'contact_person' => 'drg. Bambang',
        'phone' => '021-3456789',
    ]);

    $auditor = User::factory()->create([
        'facility_id' => $auditorFacility->id,
        'role' => 'DINKES_AUDITOR',
    ]);

    $response = $this->actingAs($auditor)->get('/audit');

    $response->assertStatus(200);
    $response->assertSee('Regional Oversight & Audit');
    $response->assertSee('System Audit Trail');
});
