<?php

use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Models\Transfer;
use App\Models\User;
use App\Services\RedistributionEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('burn rate calculation is correct', function () {
    expect(RedistributionEngine::calculateBurnRate(120, 30))->toBe(4.0)
        ->and(RedistributionEngine::calculateBurnRate(90, 30))->toBe(3.0)
        ->and(RedistributionEngine::calculateBurnRate(15, 30))->toBe(0.5);
});

test('days to deplete handles standard, zero and infinite cases', function () {
    // 480 stock, 4/day burn rate = 120 days
    expect(RedistributionEngine::calculateDaysToDeplete(480, 4.0))->toBe(120.0);

    // 0 burn rate = Infinity
    expect(RedistributionEngine::calculateDaysToDeplete(100, 0.0))->toBe(INF);

    // 0 stock = 0 days
    expect(RedistributionEngine::calculateDaysToDeplete(0, 5.0))->toBe(0.0);
});

test('PRD benchmark test case: 480 stock, 4/day burn rate, 45 days to expiry', function () {
    $stock = 480;
    $burnRate = 4.0;
    $daysToExpiry = 45;

    $daysToDeplete = RedistributionEngine::calculateDaysToDeplete($stock, $burnRate);
    expect($daysToDeplete)->toBe(120.0);

    $projectedWaste = RedistributionEngine::calculateProjectedWaste($stock, $burnRate, $daysToExpiry);
    // Consumed: 4 * 45 = 180 units. Projected Waste: 480 - 180 = 300 units.
    expect($projectedWaste)->toBe(300);

    $wasteRatio = $projectedWaste / $stock; // 300 / 480 = 0.625 (62.5%)
    expect($wasteRatio)->toBe(0.625);

    $riskLevel = RedistributionEngine::determineRiskLevel($daysToExpiry, $wasteRatio, $projectedWaste);
    // 45 days <= 60 and waste ratio 62.5% >= 25% -> HIGH
    expect($riskLevel)->toBe('HIGH');
});

test('risk levels categorize critical, medium, and low correctly', function () {
    // Critical: <= 30 days to expiry
    expect(RedistributionEngine::determineRiskLevel(25, 0.60, 100))->toBe('CRITICAL');

    // Medium: <= 90 days or any projected waste > 0
    expect(RedistributionEngine::determineRiskLevel(80, 0.15, 20))->toBe('MEDIUM');

    // Low: projected waste = 0 (fully consumed before expiry)
    expect(RedistributionEngine::determineRiskLevel(60, 0.0, 0))->toBe('LOW');
});

test('haversine distance calculates accurate geographic span', function () {
    // Monas (-6.175392, 106.827153) to RSUD Tarakan (-6.1729, 106.8122) ~ 1.67 km
    $distance = RedistributionEngine::calculateHaversineDistance(-6.175392, 106.827153, -6.1729, 106.8122);
    expect($distance)->toBeGreaterThan(1.4)
        ->and($distance)->toBeLessThan(2.0);
});

test('match score weights: 50% need, 30% proximity, 20% logistics', function () {
    $need = 80.0;
    $proximity = 90.0;
    $logistics = 100.0;

    // (0.50 * 80) + (0.30 * 90) + (0.20 * 100) = 40 + 27 + 20 = 87.0
    expect(RedistributionEngine::calculateMatchingScore($need, $proximity, $logistics))->toBe(87.0);
});

test('proximity score decays to zero at the maximum radius', function () {
    expect(RedistributionEngine::calculateProximityScore(0.0))->toBe(100.0)
        ->and(RedistributionEngine::calculateProximityScore(25.0))->toBe(50.0)
        ->and(RedistributionEngine::calculateProximityScore(50.0))->toBe(0.0)
        ->and(RedistributionEngine::calculateProximityScore(9.0, 18.0))->toBe(50.0);
});

test('nearest facilities are ranked by distance and unusable coordinates are skipped', function () {
    $origin = Facility::create([
        'name' => 'RSUD Tarakan Jakarta',
        'code' => 'FSK-RSUD-TRK',
        'type' => 'RSUD',
        'address' => 'Jl. Kyai Caringin No. 7',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1729000,
        'longitude' => 106.8122000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Budi Santoso',
        'phone' => '021-3842934',
    ]);

    $near = Facility::create([
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

    $far = Facility::create([
        'name' => 'Klinik Jakarta Selatan',
        'code' => 'FSK-KLN-JS',
        'type' => 'KLINIK',
        'address' => 'Jl. Fatmawati No. 10',
        'city' => 'Jakarta Selatan',
        'latitude' => -6.2085000,
        'longitude' => 106.8145000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Rina Kartika',
        'phone' => '021-7501234',
    ]);

    $unusable = Facility::create([
        'name' => 'Faskes Tanpa Koordinat',
        'code' => 'FSK-NO-COORD',
        'type' => 'KLINIK',
        'address' => 'Alamat belum diverifikasi',
        'city' => 'Jakarta Pusat',
        'latitude' => 0,
        'longitude' => 0,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Tanpa Nama',
        'phone' => '021-0000000',
    ]);

    $ranked = RedistributionEngine::nearestFacilities($origin, Facility::all());
    $rankedIds = array_map(fn (array $row): int => $row['facility']->id, $ranked);

    expect($ranked)->toHaveCount(2)
        ->and($ranked[0]['facility']->id)->toBe($near->id)
        ->and($ranked[1]['facility']->id)->toBe($far->id)
        ->and($ranked[0]['distance_km'])->toBeLessThan($ranked[1]['distance_km'])
        ->and($ranked[0]['proximity_score'])->toBeGreaterThan($ranked[1]['proximity_score'])
        ->and($rankedIds)->not->toContain($unusable->id);
});

test('candidate recipients can be ranked by nearest distance', function () {
    $donor = Facility::create([
        'name' => 'RSUD Tarakan Jakarta',
        'code' => 'FSK-RSUD-TRK',
        'type' => 'RSUD',
        'address' => 'Jl. Kyai Caringin No. 7',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1729000,
        'longitude' => 106.8122000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Budi Santoso',
        'phone' => '021-3842934',
    ]);

    $near = Facility::create([
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

    $far = Facility::create([
        'name' => 'Klinik Jakarta Selatan',
        'code' => 'FSK-KLN-JS',
        'type' => 'KLINIK',
        'address' => 'Jl. Fatmawati No. 10',
        'city' => 'Jakarta Selatan',
        'latitude' => -6.2085000,
        'longitude' => 106.8145000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Rina Kartika',
        'phone' => '021-7501234',
    ]);

    $medicine = Medicine::create([
        'name' => 'Ceftriaxone 1g Injeksi',
        'generic_name' => 'Ceftriaxone Sodium',
        'sku' => 'MED-CFT-001',
        'category' => 'Antibiotic',
        'dosage_form' => 'Vial Injeksi',
        'unit' => 'Vial',
        'unit_price_idr' => 45000,
        'requires_cold_chain' => false,
    ]);

    $batch = Batch::create([
        'facility_id' => $donor->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'CFT-2026-X89',
        'current_stock' => 480,
        'daily_burn_rate' => 4.0,
        'expiry_date' => now()->addDays(45)->toDateString(),
        'unit_price_idr' => 45000,
        'status' => 'ACTIVE',
    ]);

    $byDistance = RedistributionEngine::findCandidateRecipients($batch, 'distance');
    $byScore = RedistributionEngine::findCandidateRecipients($batch);

    expect($byDistance)->toHaveCount(2)
        ->and($byDistance[0]['facility']->id)->toBe($near->id)
        ->and($byDistance[0]['distance_km'])->toBeLessThanOrEqual($byDistance[1]['distance_km'])
        ->and($byScore[0]['match_score'])->toBeGreaterThanOrEqual($byScore[1]['match_score']);
});

test('recipient intake cap prevents secondary expiration', function () {
    // Projected waste is 300 units, but recipient consumes 5/day and there are 45 days left (minus 7-day buffer = 38 days)
    // Max absorbable = 5 * 38 = 190 units
    $intakeCap = RedistributionEngine::calculateRecipientIntakeCap(300, 5.0, 45, 7);
    expect($intakeCap)->toBe(190);

    // If recipient can consume 10/day (10 * 38 = 380), cap is bounded by donor waste (300)
    $boundedCap = RedistributionEngine::calculateRecipientIntakeCap(300, 10.0, 45, 7);
    expect($boundedCap)->toBe(300);
});

test('inventory invariant: stock changes atomically ONLY upon completed transfer', function () {
    $donorFacility = Facility::create([
        'name' => 'RSUD Tarakan Jakarta',
        'code' => 'FSK-RSUD-TRK',
        'type' => 'RSUD',
        'address' => 'Jl. Kyai Caringin No. 7',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1729000,
        'longitude' => 106.8122000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Budi Santoso',
        'phone' => '021-3842934',
    ]);

    $recipientFacility = Facility::create([
        'name' => 'Puskesmas Kecamatan Gambir',
        'code' => 'FSK-PKM-GMB',
        'type' => 'PUSKESMAS',
        'address' => 'Jl. Tanah Abang II No. 27',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1764000,
        'longitude' => 106.8285000,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Siti Rahma',
        'phone' => '021-3501234',
    ]);

    $user = User::factory()->create([
        'facility_id' => $recipientFacility->id,
        'role' => 'FACILITY_APPROVER',
    ]);

    $medicine = Medicine::create([
        'name' => 'Ceftriaxone 1g Injeksi',
        'generic_name' => 'Ceftriaxone Sodium',
        'sku' => 'MED-CFT-001',
        'category' => 'Antibiotic',
        'dosage_form' => 'Vial Injeksi',
        'unit' => 'Vial',
        'unit_price_idr' => 45000,
        'requires_cold_chain' => false,
    ]);

    $donorBatch = Batch::create([
        'facility_id' => $donorFacility->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'CFT-2026-X89',
        'current_stock' => 480,
        'daily_burn_rate' => 4.0,
        'expiry_date' => now()->addDays(45)->toDateString(),
        'unit_price_idr' => 45000,
        'status' => 'ACTIVE',
    ]);

    // Create Transfer in PROPOSED state
    $transfer = Transfer::create([
        'transfer_code' => 'TRF-TEST-001',
        'donor_facility_id' => $donorFacility->id,
        'recipient_facility_id' => $recipientFacility->id,
        'batch_id' => $donorBatch->id,
        'quantity' => 100,
        'status' => 'PROPOSED',
    ]);

    // INVARIANT CHECK 1: Stock has NOT changed during PROPOSED state
    expect($donorBatch->fresh()->current_stock)->toBe(480);
    expect(Batch::where('facility_id', $recipientFacility->id)->count())->toBe(0);

    // Progress through APPROVED, ACCEPTED, IN_TRANSIT
    $transfer->update(['status' => 'APPROVED']);
    expect($donorBatch->fresh()->current_stock)->toBe(480);

    $transfer->update(['status' => 'IN_TRANSIT']);
    expect($donorBatch->fresh()->current_stock)->toBe(480);

    // INVARIANT CHECK 2: Complete the transfer -> Stock changes atomically
    $result = RedistributionEngine::completeTransfer($transfer, $user);
    expect($result)->toBeTrue();

    // Donor batch deducted by 100
    expect($donorBatch->fresh()->current_stock)->toBe(380);

    // Recipient batch received 100
    $recipientBatch = Batch::where('facility_id', $recipientFacility->id)
        ->where('batch_number', 'CFT-2026-X89')
        ->first();
    expect($recipientBatch)->not->toBeNull();
    expect($recipientBatch->current_stock)->toBe(100);
    expect($transfer->fresh()->status)->toBe('COMPLETED');
});
