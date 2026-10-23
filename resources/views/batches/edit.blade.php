@extends('layouts.mediloop', ['title' => 'Edit Batch #'.$batch->batch_number])

@section('content')
@php
    $statusTone = match ($batch->status) {
        'ACTIVE' => 'active',
        'RESERVED' => 'pending',
        'DEPLETED' => 'inactive',
        'EXPIRED' => 'suspended',
        default => 'inactive',
    };
    $statusLabel = match ($batch->status) {
        'ACTIVE' => 'Aktif',
        'RESERVED' => 'Dialokasikan',
        'DEPLETED' => 'Habis',
        'EXPIRED' => 'Kedaluwarsa',
        default => $batch->status,
    };
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('batches.index') }}">Inventaris obat</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Edit batch {{ $batch->batch_number }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">Inventaris obat</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Edit Data Batch Stok</h1>
                <span class="ml-facility-chip">{{ $batch->facility->name }}</span>
            </div>
            <p class="ml-page-sub">
                Perbarui stok fisik, laju pakai, atau masa edar batch {{ $batch->batch_number }}.
            </p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M19 12H5m0 0l7 7m-7-7l7-7" />
                </svg>
                Kembali ke inventaris
            </a>
        </div>
    </header>

    <div class="ml-alert ml-alert-info" role="note">
        <strong>Perubahan akan tercatat di jejak audit</strong>
        <p class="ml-alert-copy">
            Setiap perubahan data batch dicatat atas nama {{ $user->name }}.
        </p>
    </div>

    @if($errors->any())
        <div class="ml-alert ml-alert-error" role="alert">
            <strong>Periksa kembali isian berikut:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('batches.update', $batch) }}">
        @csrf
        @method('PUT')

        <div class="ml-two-col">
            <div>
                <section class="ml-card" aria-labelledby="edit-batch-title">
                    <div class="ml-card-head">
                        <h2 class="ml-card-title" id="edit-batch-title">Perbarui data batch</h2>
                        <p class="ml-card-sub">Perubahan langsung berlaku setelah disimpan.</p>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-form-grid is-tight">
                            <div class="ml-field">
                                <label for="current_stock" class="ml-label">Stok fisik aktif <span class="ml-required">*</span></label>
                                <input
                                    type="number"
                                    id="current_stock"
                                    name="current_stock"
                                    class="ml-input is-mono @error('current_stock') is-invalid @enderror"
                                    value="{{ old('current_stock', $batch->current_stock) }}"
                                    min="0"
                                    required
                                >
                                @error('current_stock')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Jumlah yang tersedia di gudang saat ini.</p>
                            </div>

                            <div class="ml-field">
                                <label for="daily_burn_rate" class="ml-label">Laju pakai harian <span class="ml-required">*</span></label>
                                <input
                                    type="number"
                                    step="0.01"
                                    id="daily_burn_rate"
                                    name="daily_burn_rate"
                                    class="ml-input is-mono @error('daily_burn_rate') is-invalid @enderror"
                                    value="{{ old('daily_burn_rate', $batch->daily_burn_rate) }}"
                                    min="0"
                                    required
                                >
                                @error('daily_burn_rate')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Rata-rata unit yang dipakai per hari.</p>
                            </div>

                            <div class="ml-field">
                                <label for="expiry_date" class="ml-label">Tanggal kedaluwarsa <span class="ml-required">*</span></label>
                                <input
                                    type="date"
                                    id="expiry_date"
                                    name="expiry_date"
                                    class="ml-input @error('expiry_date') is-invalid @enderror"
                                    value="{{ old('expiry_date', $batch->expiry_date->toDateString()) }}"
                                    required
                                >
                                @error('expiry_date')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Tanggal berakhir masa edar obat.</p>
                            </div>

                            <div class="ml-field">
                                <label for="unit_price_idr" class="ml-label">Harga satuan batch (Rp) <span class="ml-required">*</span></label>
                                <input
                                    type="number"
                                    id="unit_price_idr"
                                    name="unit_price_idr"
                                    class="ml-input is-mono @error('unit_price_idr') is-invalid @enderror"
                                    value="{{ old('unit_price_idr', $batch->unit_price_idr) }}"
                                    min="0"
                                    required
                                >
                                @error('unit_price_idr')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Dipakai untuk estimasi nilai stok dan potensi limbah.</p>
                            </div>

                            <div class="ml-field">
                                <label for="storage_location" class="ml-label">Lokasi fisik <span class="ml-optional">(opsional)</span></label>
                                <input
                                    type="text"
                                    id="storage_location"
                                    name="storage_location"
                                    class="ml-input @error('storage_location') is-invalid @enderror"
                                    value="{{ old('storage_location', $batch->storage_location) }}"
                                    placeholder="Contoh: Rak baru 9 atau chiller 2–8°C"
                                >
                                @error('storage_location')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Rak, lemari, atau chiller tempat batch disimpan.</p>
                            </div>

                            <div class="ml-field">
                                <label for="status" class="ml-label">Status siklus batch <span class="ml-required">*</span></label>
                                <select id="status" name="status" class="ml-select @error('status') is-invalid @enderror" required>
                                    <option value="ACTIVE" @selected(old('status', $batch->status) === 'ACTIVE')>Aktif (tersedia)</option>
                                    <option value="RESERVED" @selected(old('status', $batch->status) === 'RESERVED')>Dialokasikan</option>
                                    <option value="DEPLETED" @selected(old('status', $batch->status) === 'DEPLETED')>Habis</option>
                                    <option value="EXPIRED" @selected(old('status', $batch->status) === 'EXPIRED')>Kedaluwarsa</option>
                                </select>
                                @error('status')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Status tidak mengubah jumlah stok secara otomatis.</p>
                            </div>
                        </div>

                        <div class="ml-action-row">
                            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">Batal</a>
                            <button type="submit" class="ml-btn-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan perubahan
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <aside>
                <section class="ml-card ml-sticky" aria-labelledby="batch-summary-title">
                    <div class="ml-card-head">
                        <h2 class="ml-card-title" id="batch-summary-title">Ringkasan batch</h2>
                        <p class="ml-card-sub">Identitas dan posisi stok saat ini.</p>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-summary-medicine">{{ $batch->medicine->name }}</div>
                        <p class="ml-cell-meta">
                            {{ $batch->medicine->dosage_form }}, satuan {{ $batch->medicine->unit }}@if($batch->medicine->requires_cold_chain), wajib rantai dingin @endif
                        </p>

                        <div class="ml-detail-grid ml-summary-details">
                            <div>
                                <div class="ml-detail-label">Nomor batch</div>
                                <div class="ml-detail-value is-mono">{{ $batch->batch_number }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">Status</div>
                                <div class="ml-detail-value">
                                    <span class="ml-status ml-status-{{ $statusTone }}">{{ $statusLabel }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="ml-detail-label">Fasilitas</div>
                                <div class="ml-detail-value">{{ $batch->facility->name }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">Kedaluwarsa</div>
                                <div class="ml-detail-value">{{ $batch->expiry_date->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">Stok saat ini</div>
                                <div class="ml-detail-value">{{ number_format($batch->current_stock, 0, ',', '.') }} {{ $batch->medicine->unit }}</div>
                            </div>
                            <div>
                                <div class="ml-detail-label">Harga satuan</div>
                                <div class="ml-detail-value">Rp {{ number_format($batch->unit_price_idr, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="ml-forecast-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Stok dan status diperbarui terpisah. Setiap perubahan masuk ke jejak audit.</span>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </form>
</div>
@endsection
