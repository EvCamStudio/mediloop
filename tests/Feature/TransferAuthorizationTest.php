<?php

use App\Enums\UserRole;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\Medicine;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;

/**
 * @return array{facility: Facility, staff: User, approver: User}
 */
function transferParty(string $name, string $code, string $type = 'RSUD'): array
{
    $facility = Facility::create([
        'name' => $name,
        'code' => $code,
        'type' => $type,
        'address' => 'Jl. Kesehatan No. 1',
        'city' => 'Jakarta Pusat',
        'latitude' => -6.1754,
        'longitude' => 106.8272,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. '.$name,
        'phone' => '021-1234567',
    ]);

    return [
        'facility' => $facility,
        'staff' => User::factory()->create([
            'facility_id' => $facility->id,
            'role' => UserRole::FacilityStaff->value,
        ]),
        'approver' => User::factory()->create([
            'facility_id' => $facility->id,
            'role' => UserRole::FacilityApprover->value,
        ]),
    ];
}

/**
 * @return array{transfer: Transfer, donor: array{facility: Facility, staff: User, approver: User}, recipient: array{facility: Facility, staff: User, approver: User}}
 */
function transferFixture(string $status = 'PROPOSED'): array
{
    $donor = transferParty('RSUD Donor', 'FSK-AUTH-DONOR');
    $recipient = transferParty('Puskesmas Penerima', 'FSK-AUTH-RECIPIENT', 'PUSKESMAS');

    $medicine = Medicine::create([
        'name' => 'Ceftriaxone 1g Injeksi',
        'generic_name' => 'Ceftriaxone Sodium',
        'sku' => 'MED-AUTH-001',
        'category' => 'Antibiotic',
        'dosage_form' => 'Vial Injeksi',
        'unit' => 'Vial',
        'unit_price_idr' => 45000,
    ]);

    $batch = Batch::create([
        'facility_id' => $donor['facility']->id,
        'medicine_id' => $medicine->id,
        'batch_number' => 'AUTH-BATCH-001',
        'current_stock' => 100,
        'daily_burn_rate' => 2.0,
        'expiry_date' => Carbon::today()->addDays(30)->toDateString(),
        'status' => 'ACTIVE',
    ]);

    $transfer = Transfer::create([
        'transfer_code' => 'TRF-AUTH-'.strtoupper($status),
        'donor_facility_id' => $donor['facility']->id,
        'recipient_facility_id' => $recipient['facility']->id,
        'batch_id' => $batch->id,
        'quantity' => 10,
        'status' => $status,
        'proposed_by' => $donor['staff']->id,
        'proposed_at' => now(),
    ]);

    return [
        'transfer' => $transfer,
        'donor' => $donor,
        'recipient' => $recipient,
    ];
}

test('a recipient approver cannot approve a donor transfer', function () {
    $fixture = transferFixture();

    $this->actingAs($fixture['recipient']['approver'])
        ->post(route('transfers.approve', $fixture['transfer']))
        ->assertForbidden();

    expect($fixture['transfer']->fresh()->status)->toBe('PROPOSED');
});

test('a donor staff member cannot approve a donor transfer', function () {
    $fixture = transferFixture();

    $this->actingAs($fixture['donor']['staff'])
        ->post(route('transfers.approve', $fixture['transfer']))
        ->assertForbidden();

    expect($fixture['transfer']->fresh()->status)->toBe('PROPOSED');
});

test('a donor staff member cannot confirm physical arrival at the recipient', function () {
    $fixture = transferFixture('IN_TRANSIT');

    $this->actingAs($fixture['donor']['staff'])
        ->post(route('transfers.complete', $fixture['transfer']))
        ->assertForbidden();

    expect($fixture['transfer']->fresh()->status)->toBe('IN_TRANSIT');
});

test('a transfer cannot be dispatched before the recipient accepts', function () {
    $fixture = transferFixture();

    $this->actingAs($fixture['donor']['staff'])
        ->post(route('transfers.dispatch', $fixture['transfer']), [
            'tracking_number' => 'RESI-BELUM-SIAP',
            'courier_type' => 'Kurir Farmasi',
        ])
        ->assertSessionHasErrors('status');

    expect($fixture['transfer']->fresh()->status)->toBe('PROPOSED');
});

test('the dispatch form shows the compliance notice and does not prefill a tracking number', function () {
    $fixture = transferFixture('ACCEPTED');

    $this->actingAs($fixture['donor']['staff'])
        ->get(route('transfers.show', $fixture['transfer']))
        ->assertOk()
        ->assertSee('Pharmacy regulatory compliance')
        ->assertSee('Example: EXP-DKI-2026-001')
        ->assertSee('Enter the number from the physical tracking slip or delivery note.')
        ->assertDontSee('value="EXP-DKI-', false);
});

test('only parties and auditors can view a transfer', function () {
    $fixture = transferFixture();
    $outsider = transferParty('Klinik Lain', 'FSK-AUTH-OUTSIDER', 'KLINIK')['staff'];

    $this->actingAs($outsider)
        ->get(route('transfers.show', $fixture['transfer']))
        ->assertForbidden();

    $this->actingAs($fixture['recipient']['staff'])
        ->get(route('transfers.show', $fixture['transfer']))
        ->assertOk();
});

test('transfer proposal errors render through shared validation components', function () {
    $fixture = transferFixture();
    $batch = Batch::query()->firstOrFail();

    $response = $this->actingAs($fixture['donor']['staff'])
        ->from(route('transfers.propose', ['batch' => $batch]))
        ->followingRedirects()
        ->post(route('transfers.store'), [
            'batch_id' => $batch->id,
            'recipient_facility_id' => $fixture['recipient']['facility']->id,
            'quantity' => '',
        ]);

    $response->assertOk();
    $response->assertSee('data-validate', false);
    $response->assertSee('data-form-summary', false);
    $response->assertSee('href="#quantity"', false);
    $response->assertSee('id="quantity-error"', false);
    $response->assertSee('data-field-error="quantity"', false);
});
