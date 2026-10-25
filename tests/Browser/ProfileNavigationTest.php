<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfileNavigationTest extends DuskTestCase
{
    public function test_returning_from_profile_keeps_dashboard_controls_working(): void
    {
        $user = User::query()->where('email', 'admin@mediloop.test')->first()
            ?? User::query()->where('role', 'SYSADMIN')->where('status', 'ACTIVE')->firstOrFail();

        $this->browse(function (Browser $browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/settings/profile')
                ->waitForText('Profile settings')
                ->assertVisible('input[name="name"]')
                ->clickLink('MediLoop')
                ->waitForLocation('/dashboard')
                ->waitForText('Ringkasan logistik')
                ->click('#ml-profile-btn')
                ->waitUntil("document.getElementById('ml-profile-dropdown').style.display === 'block'")
                ->assertVisible('#ml-profile-dropdown')
                ->click('#ml-settings-btn')
                ->waitUntil("document.getElementById('ml-settings-dropdown').style.display === 'block'")
                ->assertVisible('#ml-settings-dropdown');
        });
    }
}
