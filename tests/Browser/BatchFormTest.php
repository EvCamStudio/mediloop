<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BatchFormTest extends DuskTestCase
{
    private function admin(): User
    {
        return User::query()->where('email', 'admin@mediloop.test')->first()
            ?? User::query()->where('role', 'SYSADMIN')->where('status', 'ACTIVE')->firstOrFail();
    }

    public function test_preview_and_default_expiry_are_populated_on_load(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->admin())
                ->visit('/batches/create')
                ->waitFor('#batchForm')
                ->waitUntil("document.getElementById('expiry_date').value !== ''")
                ->assertSeeIn('#previewDaysExpiry', 'hari')
                ->assertSeeIn('#previewWaste', 'unit');
        });
    }

    public function test_new_medicine_toggle_swaps_sections_and_required_state(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->admin())
                ->visit('/batches/create')
                ->waitFor('#toggleNewMedicine')
                ->assertVisible('#existingMedicineSection')
                ->assertScript("document.getElementById('newMedicineSection').hidden === true")
                ->check('#toggleNewMedicine')
                ->waitUntil("document.getElementById('newMedicineSection').hidden === false")
                ->assertVisible('#newMedicineSection')
                ->assertScript("document.getElementById('existingMedicineSection').hidden === true")
                ->waitUntil("document.getElementById('medicine_id').required === false")
                ->uncheck('#toggleNewMedicine')
                ->waitUntil("document.getElementById('existingMedicineSection').hidden === false")
                ->waitUntil("document.getElementById('medicine_id').required === true")
                ->assertVisible('#existingMedicineSection');
        });
    }
}
