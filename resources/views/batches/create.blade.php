@extends('layouts.mediloop', ['title' => 'Input Stok Obat & Batch Baru'])

@section('content')
@php
    $facilityLabel = $user->isSysadmin()
        ? 'Akses administrator'
        : ($user->facility?->name ?? 'Fasilitas terdaftar');
    $selectedFacilityId = old('facility_id', $user->facility_id ?? $facilities->first()?->id);
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('batches.index') }}">Inventaris obat</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Input batch baru</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">Inventaris obat</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Input Data Stok Batch Obat</h1>
                <span class="ml-facility-chip">{{ $facilityLabel }}</span>
            </div>
            <p class="ml-page-sub">
                Catat batch masuk atau stok berjalan. Data ini menjadi dasar proyeksi risiko dan rekomendasi redistribusi.
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

    <div class="ml-alert ml-alert-info ml-compliance-banner" role="note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
            <path d="M12 8v4m0 4h.01" />
        </svg>
        <div>
            <strong>Hanya data logistik, tanpa data pasien</strong>
            <p>
                Formulir ini mencatat obat, batch, stok fisik, laju pemakaian, dan masa edar.
                Tidak memuat identitas pasien atau rekomendasi dosis.
            </p>
        </div>
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

    <form method="POST" action="{{ route('batches.store') }}" id="batchForm">
        @csrf

        <div class="ml-two-col">
            <div>
                @if($user->isSysadmin())
                    <section class="ml-card" aria-labelledby="placement-title">
                        <div class="ml-card-head">
                            <h2 class="ml-card-title" id="placement-title">Fasilitas penampung</h2>
                            <p class="ml-card-sub">Stok akan dicatat atas nama fasilitas ini.</p>
                        </div>
                        <div class="ml-card-body">
                            <div class="ml-field is-last">
                                <label for="facility_id" class="ml-label">Fasilitas kesehatan <span class="ml-required">*</span></label>
                                <select id="facility_id" name="facility_id" class="ml-select @error('facility_id') is-invalid @enderror" required>
                                    @foreach($facilities as $facility)
                                        <option value="{{ $facility->id }}" @selected((string) $selectedFacilityId === (string) $facility->id)>
                                            {{ $facility->name }} ({{ $facility->type_label }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('facility_id')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Pilih fasilitas yang akan menyimpan batch ini.</p>
                            </div>
                        </div>
                    </section>
                @endif

                <section class="ml-card" aria-labelledby="medicine-title">
                    <div class="ml-card-head">
                        <div class="ml-step-head">
                            <div class="ml-step-title">
                                <span class="ml-step-index" aria-hidden="true">1</span>
                                <div>
                                    <h2 class="ml-card-title" id="medicine-title">Identitas &amp; jenis sediaan</h2>
                                    <p class="ml-card-sub">Pilih obat dari master formularium atau daftarkan obat baru.</p>
                                </div>
                            </div>
                            <label class="ml-form-toggle" for="toggleNewMedicine">
                                <input
                                    type="checkbox"
                                    id="toggleNewMedicine"
                                    name="is_new_medicine"
                                    value="1"
                                    aria-controls="existingMedicineSection newMedicineSection"
                                    @checked(old('is_new_medicine'))
                                >
                                <span>Obat belum terdaftar di master</span>
                            </label>
                        </div>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-form-stack">
                            <div id="existingMedicineSection" @if(old('is_new_medicine')) hidden @endif>
                                <label for="medicine_id" class="ml-label">Pilih obat dari master formularium <span class="ml-required">*</span></label>
                                <select id="medicine_id" name="medicine_id" class="ml-select @error('medicine_id') is-invalid @enderror">
                                    <option value="">Pilih obat yang tersedia</option>
                                    @foreach($medicines as $medicine)
                                        <option
                                            value="{{ $medicine->id }}"
                                            data-price="{{ $medicine->unit_price_idr }}"
                                            data-unit="{{ $medicine->unit }}"
                                            data-cold="{{ $medicine->requires_cold_chain ? '1' : '0' }}"
                                            @selected((string) old('medicine_id') === (string) $medicine->id)
                                        >
                                            {{ $medicine->name }} ({{ $medicine->dosage_form }}), {{ $medicine->unit }}, Rp {{ number_format($medicine->unit_price_idr, 0, ',', '.') }}@if($medicine->requires_cold_chain), rantai dingin @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('medicine_id')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Jika obat belum tersedia di daftar, aktifkan “Obat belum terdaftar di master”.</p>
                            </div>

                            <div id="newMedicineSection" class="ml-subpanel" @if(! old('is_new_medicine')) hidden @endif>
                                <div class="ml-subpanel-head">
                                    <span class="ml-subpanel-title">Master obat baru</span>
                                </div>

                                <div class="ml-form-grid is-tight">
                                    <div class="ml-field">
                                        <label for="new_medicine_name" class="ml-label">Nama obat <span class="ml-required">*</span></label>
                                        <input
                                            type="text"
                                            id="new_medicine_name"
                                            name="new_medicine_name"
                                            class="ml-input @error('new_medicine_name') is-invalid @enderror"
                                            value="{{ old('new_medicine_name') }}"
                                            placeholder="Contoh: Ceftriaxone 1g Injeksi"
                                        >
                                        @error('new_medicine_name')<p class="ml-error-text">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_generic_name" class="ml-label">Nama generik / zat aktif <span class="ml-required">*</span></label>
                                        <input
                                            type="text"
                                            id="new_generic_name"
                                            name="new_generic_name"
                                            class="ml-input @error('new_generic_name') is-invalid @enderror"
                                            value="{{ old('new_generic_name') }}"
                                            placeholder="Contoh: Ceftriaxone Sodium"
                                        >
                                        @error('new_generic_name')<p class="ml-error-text">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_category" class="ml-label">Kategori terapeutik <span class="ml-required">*</span></label>
                                        <select id="new_category" name="new_category" class="ml-select @error('new_category') is-invalid @enderror">
                                            <option value="Antibiotic" @selected(old('new_category') === 'Antibiotic')>Antibiotik</option>
                                            <option value="Cardiovascular" @selected(old('new_category') === 'Cardiovascular')>Kardiovaskular</option>
                                            <option value="Endocrine" @selected(old('new_category') === 'Endocrine')>Endokrin</option>
                                            <option value="Analgesic" @selected(old('new_category') === 'Analgesic')>Analgesik</option>
                                            <option value="Emergency" @selected(old('new_category') === 'Emergency')>Emergensi</option>
                                            <option value="Respiratory" @selected(old('new_category') === 'Respiratory')>Respirasi</option>
                                            <option value="Other" @selected(old('new_category') === 'Other')>Lainnya</option>
                                        </select>
                                        @error('new_category')<p class="ml-error-text">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_dosage_form" class="ml-label">Bentuk sediaan <span class="ml-required">*</span></label>
                                        <input
                                            type="text"
                                            id="new_dosage_form"
                                            name="new_dosage_form"
                                            class="ml-input @error('new_dosage_form') is-invalid @enderror"
                                            value="{{ old('new_dosage_form') }}"
                                            placeholder="Vial, tablet, sirup, infus"
                                        >
                                        @error('new_dosage_form')<p class="ml-error-text">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_unit" class="ml-label">Satuan kemasan <span class="ml-required">*</span></label>
                                        <input
                                            type="text"
                                            id="new_unit"
                                            name="new_unit"
                                            class="ml-input @error('new_unit') is-invalid @enderror"
                                            value="{{ old('new_unit', 'Box') }}"
                                            placeholder="Box, vial, botol, ampul"
                                        >
                                        @error('new_unit')<p class="ml-error-text">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="ml-field">
                                        <label for="new_unit_price_idr" class="ml-label">Harga acuan satuan (Rp) <span class="ml-required">*</span></label>
                                        <input
                                            type="number"
                                            id="new_unit_price_idr"
                                            name="new_unit_price_idr"
                                            class="ml-input is-mono @error('new_unit_price_idr') is-invalid @enderror"
                                            value="{{ old('new_unit_price_idr', 50000) }}"
                                            min="0"
                                        >
                                        @error('new_unit_price_idr')<p class="ml-error-text">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="ml-field is-full">
                                        <label class="ml-checkbox-row">
                                            <input
                                                type="checkbox"
                                                id="new_requires_cold_chain"
                                                name="new_requires_cold_chain"
                                                value="1"
                                                @checked(old('new_requires_cold_chain'))
                                            >
                                            <span>Wajib rantai dingin / cold chain (2–8°C)</span>
                                        </label>
                                    </div>

                                    <div class="ml-field is-full">
                                        <label for="new_handling_instructions" class="ml-label">Petunjuk penyimpanan <span class="ml-optional">(opsional)</span></label>
                                        <input
                                            type="text"
                                            id="new_handling_instructions"
                                            name="new_handling_instructions"
                                            class="ml-input @error('new_handling_instructions') is-invalid @enderror"
                                            value="{{ old('new_handling_instructions') }}"
                                            placeholder="Contoh: Simpan pada suhu ruang kering di bawah 25°C."
                                        >
                                        @error('new_handling_instructions')<p class="ml-error-text">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ml-card" aria-labelledby="batch-title">
                    <div class="ml-card-head">
                        <div class="ml-step-head">
                            <div class="ml-step-title">
                                <span class="ml-step-index" aria-hidden="true">2</span>
                                <div>
                                    <h2 class="ml-card-title" id="batch-title">Batch, stok &amp; masa edar</h2>
                                    <p class="ml-card-sub">Catat identitas batch dan parameter pemakaian yang akan dipantau.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-form-grid is-tight">
                            <div class="ml-field">
                                <label for="batch_number" class="ml-label">Nomor batch / lot pabrik <span class="ml-required">*</span></label>
                                <input
                                    type="text"
                                    id="batch_number"
                                    name="batch_number"
                                    class="ml-input is-mono is-uppercase @error('batch_number') is-invalid @enderror"
                                    value="{{ old('batch_number') }}"
                                    placeholder="Contoh: CFT-2026-X89"
                                    autocomplete="off"
                                    spellcheck="false"
                                    required
                                >
                                @error('batch_number')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Sesuai yang tertera pada kemasan fisik obat.</p>
                            </div>

                            <div class="ml-field">
                                <label for="expiry_date" class="ml-label">Tanggal kedaluwarsa <span class="ml-required">*</span></label>
                                <input
                                    type="date"
                                    id="expiry_date"
                                    name="expiry_date"
                                    class="ml-input @error('expiry_date') is-invalid @enderror"
                                    value="{{ old('expiry_date') }}"
                                    required
                                >
                                @error('expiry_date')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Tanggal berakhir masa edar obat.</p>
                            </div>

                            <div class="ml-field">
                                <label for="current_stock" class="ml-label">Stok fisik aktif <span class="ml-required">*</span></label>
                                <input
                                    type="number"
                                    id="current_stock"
                                    name="current_stock"
                                    class="ml-input is-mono @error('current_stock') is-invalid @enderror"
                                    value="{{ old('current_stock', 100) }}"
                                    min="1"
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
                                    value="{{ old('daily_burn_rate', 2.0) }}"
                                    min="0"
                                    required
                                >
                                @error('daily_burn_rate')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Rata-rata unit yang dipakai per hari.</p>
                            </div>

                            <div class="ml-field">
                                <label for="unit_price_idr" class="ml-label">Harga satuan batch (Rp)</label>
                                <input
                                    type="number"
                                    id="unit_price_idr"
                                    name="unit_price_idr"
                                    class="ml-input is-mono @error('unit_price_idr') is-invalid @enderror"
                                    value="{{ old('unit_price_idr') }}"
                                    min="0"
                                    placeholder="Gunakan harga master jika kosong"
                                >
                                @error('unit_price_idr')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Kosongkan untuk memakai harga acuan obat.</p>
                            </div>

                            <div class="ml-field">
                                <label for="storage_location" class="ml-label">Lokasi fisik <span class="ml-optional">(opsional)</span></label>
                                <input
                                    type="text"
                                    id="storage_location"
                                    name="storage_location"
                                    class="ml-input @error('storage_location') is-invalid @enderror"
                                    value="{{ old('storage_location') }}"
                                    placeholder="Contoh: Gudang farmasi, rak injeksi B-02"
                                >
                                @error('storage_location')<p class="ml-error-text">{{ $message }}</p>@enderror
                                <p class="ml-help">Rak, lemari, atau chiller tempat batch disimpan.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside>
                <section class="ml-card ml-sticky" aria-labelledby="forecast-title">
                    <div class="ml-card-head">
                        <h2 class="ml-card-title" id="forecast-title">Proyeksi batch</h2>
                        <p class="ml-card-sub">Perkiraan sebelum data disimpan.</p>
                    </div>
                    <div class="ml-card-body">
                        <div class="ml-forecast-grid">
                            <div class="ml-forecast-cell">
                                <span class="ml-forecast-label">Sisa masa edar</span>
                                <span id="previewDaysExpiry" class="ml-forecast-value">- <small>hari</small></span>
                            </div>
                            <div class="ml-forecast-cell">
                                <span class="ml-forecast-label">Hari hingga habis</span>
                                <span id="previewDaysDeplete" class="ml-forecast-value">- <small>hari</small></span>
                            </div>
                            <div class="ml-forecast-cell is-wide">
                                <span class="ml-forecast-label">Potensi terbuang</span>
                                <span id="previewWaste" class="ml-forecast-value">- <small>unit</small></span>
                            </div>
                        </div>

                        <div class="ml-forecast-risk" aria-live="polite">
                            <span class="ml-forecast-label">Klasifikasi risiko</span>
                            <span id="previewRisk" class="ml-risk ml-risk-low">Rendah</span>
                            <p id="previewRiskNote" class="ml-forecast-hint">Isi parameter batch untuk melihat proyeksi.</p>
                        </div>

                        <div class="ml-forecast-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 19V5m0 14h16M8 15l3-4 3 2 4-6" />
                            </svg>
                            <span>Dihitung dari stok, laju pakai, dan tanggal kedaluwarsa. Bukan rekomendasi dosis.</span>
                        </div>

                        <div class="ml-action-row">
                            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">Batal</a>
                            <button type="submit" class="ml-btn-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan batch
                            </button>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleNewMedicine = document.getElementById('toggleNewMedicine');
        const existingSection = document.getElementById('existingMedicineSection');
        const newSection = document.getElementById('newMedicineSection');
        const medicineSelect = document.getElementById('medicine_id');
        const unitPriceInput = document.getElementById('unit_price_idr');

        const currentStockInput = document.getElementById('current_stock');
        const dailyBurnRateInput = document.getElementById('daily_burn_rate');
        const expiryDateInput = document.getElementById('expiry_date');

        const previewDaysExpiry = document.getElementById('previewDaysExpiry');
        const previewDaysDeplete = document.getElementById('previewDaysDeplete');
        const previewWaste = document.getElementById('previewWaste');
        const previewRisk = document.getElementById('previewRisk');
        const previewRiskNote = document.getElementById('previewRiskNote');

        const numberFormatter = new Intl.NumberFormat('id-ID');
        const decimalFormatter = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 1,
            maximumFractionDigits: 1,
        });

        function syncMedicineMode() {
            const isNewMedicine = toggleNewMedicine.checked;

            existingSection.hidden = isNewMedicine;
            newSection.hidden = ! isNewMedicine;

            if (isNewMedicine) {
                medicineSelect.removeAttribute('required');
            } else {
                medicineSelect.setAttribute('required', 'required');
            }
        }

        function localDateFromInput(value) {
            const [year, month, day] = value.split('-').map(Number);

            return new Date(year, month - 1, day);
        }

        function formatLocalDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        toggleNewMedicine.addEventListener('change', syncMedicineMode);
        syncMedicineMode();

        if (! expiryDateInput.value) {
            const defaultDate = new Date();
            defaultDate.setDate(defaultDate.getDate() + 60);
            expiryDateInput.value = formatLocalDate(defaultDate);
        }

        medicineSelect.addEventListener('change', function () {
            const selected = medicineSelect.options[medicineSelect.selectedIndex];

            if (selected && selected.dataset.price && ! unitPriceInput.value) {
                unitPriceInput.value = selected.dataset.price;
            }
        });

        function updatePreview() {
            const stock = parseInt(currentStockInput.value, 10) || 0;
            const burnRate = parseFloat(dailyBurnRateInput.value) || 0;
            const expiryValue = expiryDateInput.value;

            if (! expiryValue) {
                return;
            }

            const expiryDate = localDateFromInput(expiryValue);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const daysToExpiry = Math.max(0, Math.ceil((expiryDate.getTime() - today.getTime()) / 86400000));
            const expectedConsumption = Math.floor(burnRate * daysToExpiry);
            const projectedWaste = Math.max(0, stock - expectedConsumption);
            const wasteRatio = stock > 0 ? projectedWaste / stock : 0;

            previewDaysExpiry.innerHTML = `${numberFormatter.format(daysToExpiry)} <small>hari</small>`;

            if (burnRate > 0) {
                previewDaysDeplete.innerHTML = `${decimalFormatter.format(stock / burnRate)} <small>hari</small>`;
            } else {
                previewDaysDeplete.innerHTML = 'Tidak habis <small>0/hari</small>';
            }

            previewWaste.innerHTML = `${numberFormatter.format(projectedWaste)} <small>unit (${Math.round(wasteRatio * 100)}%)</small>`;

            let risk = {
                label: 'Rendah',
                className: 'ml-risk ml-risk-low',
                note: 'Stok diproyeksikan habis sebelum kedaluwarsa.',
            };

            if (projectedWaste > 0 && daysToExpiry <= 30) {
                risk = {
                    label: 'Kritis',
                    className: 'ml-risk ml-risk-critical',
                    note: 'Sisa masa edar ≤30 hari; prioritaskan redistribusi.',
                };
            } else if (projectedWaste > 0 && (daysToExpiry <= 60 || wasteRatio >= 0.25)) {
                risk = {
                    label: 'Tinggi',
                    className: 'ml-risk ml-risk-high',
                    note: 'Risiko tinggi; evaluasi kebutuhan redistribusi.',
                };
            } else if (projectedWaste > 0) {
                risk = {
                    label: 'Sedang',
                    className: 'ml-risk ml-risk-medium',
                    note: 'Pantau laju pakai dan sisa masa edar.',
                };
            }

            previewRisk.textContent = risk.label;
            previewRisk.className = risk.className;
            previewRiskNote.textContent = risk.note;
        }

        currentStockInput.addEventListener('input', updatePreview);
        dailyBurnRateInput.addEventListener('input', updatePreview);
        expiryDateInput.addEventListener('change', updatePreview);

        updatePreview();
    });
</script>
@endsection
