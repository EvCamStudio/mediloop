<?php

namespace Tests\Browser;

use App\Models\Batch;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FormValidationTest extends DuskTestCase
{
    private function admin(): User
    {
        return User::query()->where('email', 'admin@mediloop.test')->first()
            ?? User::query()->where('role', 'SYSADMIN')->where('status', 'ACTIVE')->firstOrFail();
    }

    public function test_create_form_shows_inline_errors_without_reloading(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->admin())
                ->visit('/batches/create')
                ->waitFor('#batch_number')
                ->click('#batchForm button[type="submit"]')
                ->waitUntil("document.getElementById('batch_number-error').hidden === false")
                ->assertSeeIn('#batch_number-error', 'Nomor batch wajib diisi.')
                ->assertSeeIn('#medicine_id-error', 'Pilih obat terlebih dahulu.')
                ->assertVisible('[data-form-summary]')
                ->assertPathIs('/batches/create')
                ->type('#batch_number', 'TEST-VALID-001')
                ->waitUntil("document.getElementById('batch_number-error').hidden === true");
        });
    }

    public function test_edit_form_shows_required_message_without_reloading(): void
    {
        $donor = User::query()->where('email', 'donor@mediloop.test')->first();
        $batch = $donor?->facility_id
            ? Batch::query()->where('facility_id', $donor->facility_id)->first()
            : null;

        if (! $donor || ! $batch) {
            $this->markTestSkipped('Seeded donor account or batch data is not available.');
        }

        $this->browse(function (Browser $browser) use ($donor, $batch): void {
            $browser->loginAs($donor)
                ->visit("/batches/{$batch->id}/edit")
                ->waitFor('#current_stock')
                ->clear('#current_stock')
                ->click('form[data-validate] button[type="submit"]')
                ->waitUntil("document.getElementById('current_stock-error').hidden === false")
                ->assertSeeIn('#current_stock-error', 'Stok fisik wajib diisi.')
                ->assertPathIs("/batches/{$batch->id}/edit");
        });
    }

    public function test_team_invite_form_fills_external_summary(): void
    {
        $approver = User::query()->where('email', 'donor.head@mediloop.test')->first();

        if (! $approver) {
            $this->markTestSkipped('Seeded facility approver account is not available.');
        }

        $this->browse(function (Browser $browser) use ($approver): void {
            $browser->loginAs($approver)
                ->visit('/facility/team')
                ->waitFor('#teamInviteForm')
                ->clear('#email')
                ->click('#teamInviteForm button[type="submit"]')
                ->waitUntil("document.getElementById('email-error').hidden === false")
                ->assertSeeIn('#email-error', 'Email calon anggota wajib diisi.')
                ->assertVisible('[data-form-summary]')
                ->assertSeeIn('[data-form-summary]', 'Email calon anggota wajib diisi.')
                ->assertPathIs('/facility/team');
        });
    }

    public function test_auth_login_form_shows_inline_errors_without_reloading(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->logout()
                ->visit('/login')
                ->waitFor('#loginForm')
                ->clear('#email')
                ->click('#loginForm button[type="submit"]')
                ->waitUntil("document.getElementById('email-error').hidden === false")
                ->assertSeeIn('#email-error', 'Email wajib diisi.')
                ->assertSeeIn('#password-error', 'Password wajib diisi.')
                ->assertVisible('[data-form-summary]')
                ->assertSeeIn('[data-form-summary]', 'Email wajib diisi.')
                ->assertPathIs('/login');
        });
    }
}
