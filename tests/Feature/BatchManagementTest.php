<?php

use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected from batch management routes', function () {
    $this->get('/batches')->assertRedirect(route('login'));
    $this->get('/batches/create')->assertRedirect(route('login'));
    $this->post('/batches', [])->assertRedirect(route('login'));
});

test('staff can view batches index with search and filters', function () {
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

    $response = $this->actingAs($user)->get('/batches');
    $response->assertStatus(200);
    $response->assertSee('CFT-2026-X89');
    $response->assertSee('Ceftriaxone 1g Injeksi');
    $response->assertSee('480');
});

test('staff can access batch create form but auditor is forbidden', function () {
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

    $staff = User::factory()->create(['facility_id' => $facility->id, 'role' => 'FACILITY_STAFF']);
    $auditor = User::factory()->create(['facility_id' => $facility->id, 'role' => 'DINKES_AUDITOR']);

    $this->actingAs($staff)->get('/batches/create')->assertStatus(200)->assertSee('Input Data Stok Batch Obat');
    $this->actingAs($auditor)->get('/batches/create')->assertStatus(403);
    $this->actingAs($auditor)->post('/batches', [])->assertStatus(403);
});

test('sysadmin can input a batch for a selected facility', function () {
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

    $sysadmin = User::factory()->create(['facility_id' => null, 'role' => 'SYSADMIN']);

    $medicine = Medicine::create([
        'name' => 'Ceftriaxone 1g Injeksi',
        'generic_name' => 'Ceftriaxone Sodium',
        'sku' => 'MED-CFT-SYS',
        'category' => 'Antibiotic',
        'dosage_form' => 'Vial Injeksi',
        'unit' => 'Vial',
        'unit_price_idr' => 45000,
    ]);

    $this->actingAs($sysadmin)
        ->get('/batches/create')
        ->assertOk()
        ->assertSee('Fasilitas penampung')
        ->assertSee($facility->name);

    $response = $this->actingAs($sysadmin)->post('/batches', [
        'facility_id' => $facility->id,
        'is_new_medicine' => 0,
        'medicine_id' => $medicine->id,
        'batch_number' => 'SYS-2026-001',
        'current_stock' => 90,
        'daily_burn_rate' => 1.5,
        'expiry_date' => Carbon::today()->addDays(60)->toDateString(),
        'storage_location' => 'Gudang Farmasi Utama',
    ]);

    $response->assertRedirect(route('batches.index'));

    $batch = Batch::where('batch_number', 'SYS-2026-001')->first();
    expect($batch)->not->toBeNull()
        ->and($batch->facility_id)->toBe($facility->id)
        ->and($batch->current_stock)->toBe(90);

    $auditLog = AuditLog::where('action', 'BATCH_REGISTERED')->latest('id')->first();
    expect($auditLog)->not->toBeNull()
        ->and($auditLog->actor_id)->toBe($sysadmin->id)
        ->and($auditLog->facility_id)->toBe($facility->id);
});

test('staff can input a new batch for existing medicine', function () {
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

    $user = User::factory()->create(['facility_id' => $facility->id, 'role' => 'FACILITY_STAFF']);

    $medicine = Medicine::create([
        'name' => 'Amoxicillin 500mg Kaplet',
        'generic_name' => 'Amoxicillin Trihydrate',
        'sku' => 'MED-AMX-500',
        'category' => 'Antibiotic',
        'dosage_form' => 'Kaplet',
        'unit' => 'Box',
        'unit_price_idr' => 65000,
    ]);

    $response = $this->actingAs($user)->post('/batches', [
        'is_new_medicine' => 0,
        'medicine_id' => $medicine->id,
        'batch_number' => 'AMX-2026-N01',
        'current_stock' => 250,
        'daily_burn_rate' => 5.0,
        'expiry_date' => Carbon::today()->addDays(60)->toDateString(),
        'storage_location' => 'Rak Antibiotik A-01',
    ]);

    $response->assertRedirect(route('batches.index'));

    $batch = Batch::where('batch_number', 'AMX-2026-N01')->first();
    expect($batch)->not->toBeNull()
        ->and($batch->current_stock)->toBe(250)
        ->and($batch->facility_id)->toBe($facility->id);

    $auditLog = AuditLog::where('action', 'BATCH_REGISTERED')->first();
    expect($auditLog)->not->toBeNull();
});

test('staff can input a new batch with new on-the-fly medicine registration', function () {
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

    $user = User::factory()->create(['facility_id' => $facility->id, 'role' => 'FACILITY_STAFF']);

    $response = $this->actingAs($user)->post('/batches', [
        'is_new_medicine' => 1,
        'new_medicine_name' => 'Azithromycin 500mg Tablet',
        'new_generic_name' => 'Azithromycin Dihydrate',
        'new_category' => 'Antibiotic',
        'new_dosage_form' => 'Tablet Salut',
        'new_unit' => 'Box (30 Tablet)',
        'new_unit_price_idr' => 85000,
        'new_requires_cold_chain' => 0,
        'batch_number' => 'AZT-2026-09',
        'current_stock' => 150,
        'daily_burn_rate' => 3.0,
        'expiry_date' => Carbon::today()->addDays(50)->toDateString(),
        'storage_location' => 'Rak Tablet C-02',
    ]);

    $response->assertRedirect(route('batches.index'));

    $med = Medicine::where('name', 'Azithromycin 500mg Tablet')->first();
    expect($med)->not->toBeNull()
        ->and($med->category)->toBe('Antibiotic');

    $batch = Batch::where('batch_number', 'AZT-2026-09')->first();
    expect($batch)->not->toBeNull()
        ->and($batch->medicine_id)->toBe($med->id);
});

test('staff can update existing batch details', function () {
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

    $user = User::factory()->create(['facility_id' => $facility->id, 'role' => 'FACILITY_STAFF']);

    $medicine = Medicine::create([
        'name' => 'Amoxicillin 500mg',
        'generic_name' => 'Amoxicillin',
        'sku' => 'MED-AMX-001',
        'category' => 'Antibiotic',
        'dosage_form' => 'Kaplet',
        'unit' => 'Box',
        'unit_price_idr' => 50000,
    ]);

    $batch = Batch::create([
        'facility_id' => $facility->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'AMX-001',
        'current_stock' => 100,
        'daily_burn_rate' => 2.0,
        'expiry_date' => Carbon::today()->addDays(30)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    $response = $this->actingAs($user)->put("/batches/{$batch->id}", [
        'current_stock' => 120,
        'daily_burn_rate' => 3.5,
        'expiry_date' => Carbon::today()->addDays(40)->toDateString(),
        'unit_price_idr' => 55000,
        'storage_location' => 'Rak Baru 9',
        'status' => 'ACTIVE',
    ]);

    $response->assertRedirect(route('batches.index'));
    expect($batch->fresh()->current_stock)->toBe(120);
    expect((float) $batch->fresh()->daily_burn_rate)->toBe(3.5);
});

test('staff can delete batch without active transfers', function () {
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

    $user = User::factory()->create(['facility_id' => $facility->id, 'role' => 'FACILITY_STAFF']);

    $medicine = Medicine::create([
        'name' => 'Paracetamol 500mg',
        'generic_name' => 'Paracetamol',
        'sku' => 'MED-PCT-001',
        'category' => 'Analgesic',
        'dosage_form' => 'Tablet',
        'unit' => 'Box',
        'unit_price_idr' => 20000,
    ]);

    $batch = Batch::create([
        'facility_id' => $facility->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'PCT-DEL-01',
        'current_stock' => 50,
        'daily_burn_rate' => 1.0,
        'expiry_date' => Carbon::today()->addDays(20)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    $response = $this->actingAs($user)->delete("/batches/{$batch->id}");
    $response->assertRedirect(route('batches.index'));

    expect(Batch::find($batch->id))->toBeNull();
});

test('staff cannot delete batch with active transfer', function () {
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
        'name' => 'Puskesmas Gambir',
        'code' => 'FSK-PKM-GMB',
        'type' => 'PUSKESMAS',
        'address' => 'Jl. Tanah Abang II',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1764000,
        'longitude' => 106.8285000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Dewi',
        'phone' => '021-3501234',
    ]);

    $user = User::factory()->create(['facility_id' => $donor->id, 'role' => 'FACILITY_STAFF']);

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
        'batch_number' => 'CFT-ACTIVE-TRF',
        'current_stock' => 100,
        'daily_burn_rate' => 2.0,
        'expiry_date' => Carbon::today()->addDays(30)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    Transfer::create([
        'transfer_code' => 'TRF-TEST-001',
        'donor_facility_id' => $donor->id,
        'recipient_facility_id' => $recipient->id,
        'batch_id' => $batch->id,
        'quantity' => 20,
        'status' => 'PROPOSED',
        'proposed_by' => $user->id,
        'proposed_at' => now(),
    ]);

    $response = $this->actingAs($user)->delete("/batches/{$batch->id}");
    $response->assertSessionHasErrors('delete_error');

    expect(Batch::find($batch->id))->not->toBeNull();
});

test('batches index renders top risk candidates recommendations without errors', function () {
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
        'name' => 'Puskesmas Gambir',
        'code' => 'FSK-PUSK-GMB',
        'type' => 'PUSKESMAS',
        'address' => 'Jl. Tanah Abang II No. 33',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1754000,
        'longitude' => 106.8190000,
        'cold_chain_ready' => true,
        'contact_person' => 'drg. Maya',
        'phone' => '021-3456789',
    ]);

    $user = User::factory()->create([
        'facility_id' => $donor->id,
        'role' => 'FACILITY_STAFF',
    ]);

    $medicine = Medicine::create([
        'name' => 'Amoxicillin 500mg',
        'generic_name' => 'Amoxicillin',
        'sku' => 'MED-AMX-001',
        'category' => 'Antibiotic',
        'dosage_form' => 'Kapsul',
        'unit' => 'Kapsul',
        'unit_price_idr' => 1200,
    ]);

    Batch::create([
        'facility_id' => $donor->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'AMX-RISK-01',
        'current_stock' => 1000,
        'daily_burn_rate' => 2.0,
        'expiry_date' => Carbon::today()->addDays(20)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    $response = $this->actingAs($user)->get('/batches');
    $response->assertStatus(200);
    $response->assertSee('REKOMENDASI MATCH UTAMA');
    $response->assertSee('Puskesmas Gambir');
    $response->assertSee('Batas Alokasi:');
});
