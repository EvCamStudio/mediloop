<?php

use App\Enums\FacilityStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Mail\FacilityInvitationMail;
use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\FacilityInvitation;
use App\Models\FacilityRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function facilityPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'RSUD Contoh Sehat',
        'code' => 'FSK-RSUD-CONTOH',
        'type' => 'RSUD',
        'ownership' => 'PEMERINTAH',
        'license_number' => '445/001/CONTOH/2026',
        'address' => 'Jl. Kesehatan No. 1',
        'city' => 'Jakarta Pusat',
        'province' => 'DKI Jakarta',
        'district' => 'Gambir',
        'latitude' => -6.1754,
        'longitude' => 106.8272,
        'cold_chain_ready' => true,
        'contact_person' => 'Apt. Contoh',
        'phone' => '021-1234567',
        'email' => 'faskes@example.test',
    ], $overrides);
}

function makeFacility(array $overrides = []): Facility
{
    return Facility::create(facilityPayload($overrides));
}

function makeSysadmin(): User
{
    return User::factory()->create([
        'role' => UserRole::Sysadmin->value,
        'status' => UserStatus::Active->value,
        'facility_id' => null,
    ]);
}

test('the public facility request form can be rendered', function () {
    $this->get(route('facility-requests.create'))
        ->assertOk()
        ->assertSee('Ajukan fasilitas kesehatan Anda');
});

test('a guest can submit a facility request', function () {
    $this->post(route('facility-requests.store'), facilityPayload())
        ->assertRedirect(route('facility-requests.create'))
        ->assertSessionHas('facility_request_submitted', true);

    $this->assertDatabaseHas('facility_requests', [
        'code' => 'FSK-RSUD-CONTOH',
        'status' => 'PENDING',
    ]);
});

test('a facility request code must be unique', function () {
    makeFacility(['code' => 'FSK-DUPLIKAT']);

    $this->post(route('facility-requests.store'), facilityPayload(['code' => 'fsk-duplikat']))
        ->assertSessionHasErrors('code');
});

test('only a sysadmin can access platform administration', function () {
    $staff = User::factory()->create([
        'role' => UserRole::FacilityStaff->value,
        'facility_id' => makeFacility()->id,
    ]);

    $this->actingAs($staff)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs(makeSysadmin())->get(route('admin.dashboard'))->assertOk();
});

test('a sysadmin can create and verify a facility', function () {
    $admin = makeSysadmin();

    $this->actingAs($admin)
        ->post(route('admin.facilities.store'), facilityPayload())
        ->assertRedirect();

    $facility = Facility::where('code', 'FSK-RSUD-CONTOH')->firstOrFail();

    expect($facility->status)->toBe(FacilityStatus::Pending->value);

    $this->actingAs($admin)
        ->post(route('admin.facilities.verify', $facility))
        ->assertRedirect();

    $facility->refresh();

    expect($facility->status)->toBe(FacilityStatus::Active->value)
        ->and($facility->verified_by)->toBe($admin->id);

    $this->assertDatabaseHas('audit_logs', [
        'facility_id' => $facility->id,
        'action' => 'FACILITY_VERIFIED',
    ]);
});

test('a sysadmin can approve a facility request', function () {
    $admin = makeSysadmin();

    $this->post(route('facility-requests.store'), facilityPayload())->assertRedirect();

    $requestRecord = FacilityRequest::firstOrFail();

    $this->actingAs($admin)
        ->post(route('admin.facility-requests.approve', $requestRecord))
        ->assertRedirect();

    $requestRecord->refresh();

    expect($requestRecord->status)->toBe('APPROVED')
        ->and($requestRecord->facility_id)->not->toBeNull();

    $this->assertDatabaseHas('facilities', [
        'id' => $requestRecord->facility_id,
        'status' => FacilityStatus::Active->value,
    ]);
});

test('a facility request can be rejected with a reason', function () {
    $admin = makeSysadmin();

    $this->post(route('facility-requests.store'), facilityPayload())->assertRedirect();
    $requestRecord = FacilityRequest::firstOrFail();

    $this->actingAs($admin)
        ->post(route('admin.facility-requests.reject', $requestRecord), [
            'rejection_reason' => 'Nomor izin tidak sesuai dengan dokumen resmi.',
        ])
        ->assertRedirect(route('admin.facility-requests.index'));

    expect($requestRecord->refresh()->status)->toBe('REJECTED')
        ->and($requestRecord->rejection_reason)->toContain('Nomor izin');
});

test('an invited approver can activate an account and join the facility', function () {
    Mail::fake();

    $admin = makeSysadmin();
    $facility = makeFacility();

    $this->actingAs($admin)
        ->post(route('admin.facilities.invitations.store', $facility), [
            'email' => 'kepala@example.test',
            'role' => UserRole::FacilityApprover->value,
        ])
        ->assertRedirect();

    $token = null;

    Mail::assertSent(FacilityInvitationMail::class, function (FacilityInvitationMail $mail) use (&$token): bool {
        $token = $mail->plainToken;

        return true;
    });

    expect($token)->not->toBeNull();

    $this->get(route('invitations.show', ['token' => $token]))
        ->assertOk()
        ->assertSee($facility->name);

    $this->post(route('invitations.accept', ['token' => $token]), [
        'name' => 'Kepala Fasilitas',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    $user = User::where('email', 'kepala@example.test')->firstOrFail();

    expect($user->facility_id)->toBe($facility->id)
        ->and($user->role)->toBe(UserRole::FacilityApprover->value)
        ->and($user->status)->toBe(UserStatus::Active->value);

    $this->post(route('invitations.accept', ['token' => $token]), [
        'name' => 'Kepala Fasilitas',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('token');
});

test('an invitation for a suspended facility cannot be used', function () {
    $facility = makeFacility(['status' => FacilityStatus::Suspended->value]);
    $token = 'undangan-fasilitas-suspended';

    FacilityInvitation::create([
        'facility_id' => $facility->id,
        'email' => 'tertunda@example.test',
        'role' => UserRole::FacilityStaff->value,
        'token' => FacilityInvitation::hashToken($token),
        'invited_by' => makeSysadmin()->id,
        'expires_at' => now()->addDay(),
    ]);

    $this->get(route('invitations.show', ['token' => $token]))
        ->assertOk()
        ->assertSee('Fasilitas ini sedang tidak aktif');

    $this->post(route('invitations.accept', ['token' => $token]), [
        'name' => 'Staf Tertunda',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('token');

    expect(User::where('email', 'tertunda@example.test')->exists())->toBeFalse();
});

test('administrator registry, review, and user pages render', function () {
    $admin = makeSysadmin();
    $facility = makeFacility(['code' => 'FSK-RENDER-ADMIN']);

    $this->post(route('facility-requests.store'), facilityPayload([
        'code' => 'FSK-RENDER-REQUEST',
        'name' => 'RS Pengajuan Render',
    ]))->assertRedirect();

    $requestRecord = FacilityRequest::firstOrFail();

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('admin.facilities.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.facilities.create'))->assertOk();
    $this->actingAs($admin)->get(route('admin.facilities.show', $facility))->assertOk();
    $this->actingAs($admin)->get(route('admin.facilities.edit', $facility))->assertOk();
    $this->actingAs($admin)->get(route('admin.facility-requests.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.facility-requests.show', $requestRecord))->assertOk();
    $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
});

test('a facility approver can view and update facility settings', function () {
    $facility = makeFacility(['code' => 'FSK-SETTINGS']);
    $approver = User::factory()->create([
        'role' => UserRole::FacilityApprover->value,
        'facility_id' => $facility->id,
    ]);

    $this->actingAs($approver)
        ->get(route('facility.settings.edit'))
        ->assertOk()
        ->assertSee($facility->name);

    $this->actingAs($approver)
        ->put(route('facility.settings.update'), [
            'address' => 'Jl. Baru No. 10',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'district' => 'Gambir',
            'contact_person' => 'Apt. Baru',
            'phone' => '021-9999999',
            'email' => 'baru@example.test',
            'cold_chain_ready' => true,
        ])
        ->assertRedirect();

    expect($facility->refresh()->address)->toBe('Jl. Baru No. 10')
        ->and($facility->contact_person)->toBe('Apt. Baru');

    $this->assertDatabaseHas('audit_logs', [
        'facility_id' => $facility->id,
        'action' => 'FACILITY_UPDATED',
    ]);
});

test('a rejected request code can be submitted again', function () {
    $admin = makeSysadmin();

    $this->post(route('facility-requests.store'), facilityPayload([
        'code' => 'FSK-RESUBMIT',
        'name' => 'RS Resubmit',
    ]))->assertRedirect();

    $requestRecord = FacilityRequest::firstOrFail();

    $this->actingAs($admin)
        ->post(route('admin.facility-requests.reject', $requestRecord), [
            'rejection_reason' => 'Alamat belum lengkap dan perlu diperbaiki.',
        ])
        ->assertRedirect();

    $this->post(route('facility-requests.store'), facilityPayload([
        'code' => 'FSK-RESUBMIT',
        'name' => 'RS Resubmit',
    ]))->assertSessionHasNoErrors();

    expect(FacilityRequest::where('code', 'FSK-RESUBMIT')->count())->toBe(2);
});

test('a facility approver can invite a staff member but cannot invite another approver', function () {
    Mail::fake();

    $facility = makeFacility();
    $approver = User::factory()->create([
        'role' => UserRole::FacilityApprover->value,
        'facility_id' => $facility->id,
    ]);

    $this->actingAs($approver)
        ->get(route('facility.team.index'))
        ->assertOk()
        ->assertSee('Kirim undangan');

    $this->actingAs($approver)
        ->post(route('facility.team.invite'), [
            'email' => 'staf@example.test',
            'role' => UserRole::FacilityApprover->value,
        ])
        ->assertSessionHasErrors('role');

    $this->actingAs($approver)
        ->post(route('facility.team.invite'), [
            'email' => 'staf@example.test',
            'role' => UserRole::FacilityStaff->value,
        ])
        ->assertRedirect();

    $invitation = FacilityInvitation::firstOrFail();

    expect($invitation->email)->toBe('staf@example.test')
        ->and($invitation->facility_id)->toBe($facility->id)
        ->and($invitation->accepted_at)->toBeNull();

    Mail::assertSent(FacilityInvitationMail::class);
});

test('a suspended facility cannot use operational features', function () {
    $facility = makeFacility(['status' => FacilityStatus::Suspended->value]);
    $staff = User::factory()->create([
        'role' => UserRole::FacilityStaff->value,
        'facility_id' => $facility->id,
    ]);

    $this->actingAs($staff)->get(route('dashboard'))->assertForbidden();
});

test('a suspended user is logged out', function () {
    $user = User::factory()->create([
        'role' => UserRole::FacilityStaff->value,
        'status' => UserStatus::Suspended->value,
        'facility_id' => makeFacility()->id,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('facility onboarding events are written to the audit log', function () {
    $admin = makeSysadmin();
    $facility = makeFacility();

    $this->actingAs($admin)
        ->post(route('admin.facilities.suspend', $facility))
        ->assertRedirect();

    expect(AuditLog::where('facility_id', $facility->id)->where('action', 'FACILITY_SUSPENDED')->exists())->toBeTrue();
});
