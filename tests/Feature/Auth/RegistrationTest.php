<?php

use App\Models\Facility;
use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());

    $this->facility = Facility::create([
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
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('Daftar Akun Faskes');
    $response->assertSee('RSUD Tarakan Jakarta');
});

test('new users can register with a verified facility and staff role', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'facility_id' => $this->facility->id,
        'role' => 'RECIPIENT_STAFF',
        'job_title' => 'Staf Farmasi',
        'license_sip' => 'SIPA.3171.01.2024.0001',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->firstOrFail();

    expect($user->facility_id)->toBe($this->facility->id)
        ->and($user->role)->toBe('RECIPIENT_STAFF')
        ->and($user->job_title)->toBe('Staf Farmasi')
        ->and($user->license_sip)->toBe('SIPA.3171.01.2024.0001');
});

test('registration requires a verified facility', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'role' => 'DONOR_STAFF',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('facility_id');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

test('registration cannot escalate to privileged roles', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'facility_id' => $this->facility->id,
        'role' => 'DINKES_AUDITOR',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('role');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});
