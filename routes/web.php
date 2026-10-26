<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\FacilityRequestController as AdminFacilityRequestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::view('/', 'welcome')->name('home');

// Interface language preference (public and authenticated visitors)
Route::post('/locale', [LocaleController::class, 'update'])
    ->middleware('throttle:30,1')
    ->name('locale.update');

// Laravel Boost fallback to prevent MethodNotAllowed error on browser access
Route::get('/_boost/browser-logs', function () {
    return response('The /_boost/browser-logs endpoint only supports POST requests. Please use the Boost tools to access logs.', 405);
});

// Public facility onboarding request

Route::get('/facility-request', [FacilityRequestController::class, 'create'])->name('facility-requests.create');
Route::post('/facility-request', [FacilityRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('facility-requests.store');

// Invitation-based staff onboarding
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}', [InvitationController::class, 'accept'])
    ->middleware('throttle:10,1')
    ->name('invitations.accept');

// Protected Healthcare Decision-Support Routes
Route::middleware(['auth', 'active', 'facility.active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/network', [NetworkController::class, 'index'])->name('network.index');

    // Pharmaceutical Inventory & Batch Data Management
    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/csv-template', [BatchController::class, 'downloadTemplate'])->name('batches.csvTemplate');
    Route::post('/batches/import-csv', [BatchController::class, 'importCsv'])->name('batches.importCsv');
    Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create');
    Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
    Route::get('/batches/{batch}/edit', [BatchController::class, 'edit'])->name('batches.edit');
    Route::put('/batches/{batch}', [BatchController::class, 'update'])->name('batches.update');
    Route::delete('/batches/{batch}', [BatchController::class, 'destroy'])->name('batches.destroy');

    // Transfer Decision-Support & State Machine
    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('/transfers/propose/{batch}', [TransferController::class, 'propose'])->name('transfers.propose');
    Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
    Route::get('/transfers/{transfer}', [TransferController::class, 'show'])->name('transfers.show');
    Route::post('/transfers/{transfer}/approve', [TransferController::class, 'approve'])->name('transfers.approve');
    Route::post('/transfers/{transfer}/accept', [TransferController::class, 'accept'])->name('transfers.accept');
    Route::post('/transfers/{transfer}/dispatch', [TransferController::class, 'dispatchShipment'])->name('transfers.dispatch');
    Route::post('/transfers/{transfer}/complete', [TransferController::class, 'complete'])->name('transfers.complete');
    Route::post('/transfers/{transfer}/cancel', [TransferController::class, 'cancel'])->name('transfers.cancel');

    // Regional Dinkes Audit Oversight
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
});

// Facility team & profile management (approver)
Route::middleware(['auth', 'active', 'facility.approver'])->group(function () {
    Route::get('/facility/team', [FacilityTeamController::class, 'index'])->name('facility.team.index');
    Route::post('/facility/team/invitations', [FacilityTeamController::class, 'store'])->name('facility.team.invite');
    Route::delete('/facility/team/invitations/{invitation}', [FacilityTeamController::class, 'destroy'])
        ->name('facility.team.invitations.destroy');
    Route::get('/facility/settings', [FacilitySettingsController::class, 'edit'])->name('facility.settings.edit');
    Route::put('/facility/settings', [FacilitySettingsController::class, 'update'])->name('facility.settings.update');
});

// Platform administration
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'sysadmin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('facilities', AdminFacilityController::class)->except(['destroy']);
    Route::post('/facilities/{facility}/verify', [AdminFacilityController::class, 'verify'])->name('facilities.verify');
    Route::post('/facilities/{facility}/suspend', [AdminFacilityController::class, 'suspend'])->name('facilities.suspend');
    Route::post('/facilities/{facility}/reactivate', [AdminFacilityController::class, 'reactivate'])->name('facilities.reactivate');
    Route::post('/facilities/{facility}/invitations', [AdminFacilityController::class, 'invite'])->name('facilities.invitations.store');
    Route::delete('/facilities/{facility}/invitations/{invitation}', [AdminFacilityController::class, 'revokeInvitation'])->name('facilities.invitations.destroy');

    Route::get('/facility-requests', [AdminFacilityRequestController::class, 'index'])->name('facility-requests.index');
    Route::get('/facility-requests/{facilityRequest}', [AdminFacilityRequestController::class, 'show'])->name('facility-requests.show');
    Route::post('/facility-requests/{facilityRequest}/approve', [AdminFacilityRequestController::class, 'approve'])->name('facility-requests.approve');
    Route::post('/facility-requests/{facilityRequest}/reject', [AdminFacilityRequestController::class, 'reject'])->name('facility-requests.reject');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
});

require __DIR__.'/settings.php';
