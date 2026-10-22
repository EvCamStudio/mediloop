<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::view('/', 'welcome')->name('home');

// Protected Healthcare Decision-Support Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/network', [NetworkController::class, 'index'])->name('network.index');

    // Pharmaceutical Inventory & Batch Data Management
    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
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

require __DIR__.'/settings.php';
