<?php

use App\Models\User;
use Livewire\Livewire;

test('guests can switch the interface locale', function () {
    $this->from(route('home'))
        ->post(route('locale.update'), ['locale' => 'id'])
        ->assertRedirect(route('home'))
        ->assertSessionHas('locale', 'id');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(__('landing.nav.simulator', [], 'id'));
});

test('authenticated users have their locale persisted to the database', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('locale.update'), ['locale' => 'id'])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('locale', 'id');

    expect($user->refresh()->locale)->toBe('id');
});

test('unsupported locales are rejected', function () {
    $this->from(route('home'))
        ->post(route('locale.update'), ['locale' => 'fr'])
        ->assertRedirect(route('home'))
        ->assertSessionHasErrors('locale');

    expect(session('locale'))->toBeNull();
});

test('language settings page persists the selected locale', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user);

    Livewire::test('pages::settings.language')
        ->set('locale', 'id')
        ->call('updateLocale')
        ->assertHasNoErrors();

    expect($user->refresh()->locale)->toBe('id');
});
