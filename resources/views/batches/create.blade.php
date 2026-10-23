@extends('layouts.mediloop', ['title' => 'Input Stok Obat & Batch Baru'])

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Breadcrumb -->
    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--ml-muted);">
        <a href="{{ route('dashboard') }}" style="color: var(--ml-primary); text-decoration: none;">Dashboard</a>
        <span style="color: var(--ml-subtle);">/</span>
        <a href="{{ route('batches.index') }}" style="color: var(--ml-primary); text-decoration: none;">Inventaris Obat</a>
        <span style="color: var(--ml-subtle);">/</span>
        <span style="color: var(--ml-text); font-weight: 600;">Input Stok Obat &amp; Batch Baru</span>
    </div>

    <!-- Header -->
    <div style="padding-bottom: 16px; border-bottom: 1px solid var(--ml-border); display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 12px;">
        <div>
            <h1 style="font-size: 20px; font-weight: 800; color: var(--ml-text); margin: 0; font-family: var(--font-heading); letter-spacing: -0.02em;">Input Data Stok Batch Obat</h1>
            <p style="font-size: 12.5px; color: var(--ml-muted); margin: 4px 0 0;">
                Catat batch obat masuk atau stok berjalan untuk fasilitas: <strong style="color: var(--ml-text);">{{ $user->facility?->name ?? 'Fasilitas Terdaftar' }}</strong>.
            </p>
        </div>
        <a href="{{ route('batches.index') }}" class="ml-btn-ghost" style="padding: 7px 14px; font-size: 12px; flex-shrink: 0;">
            &larr; Kembali ke Inventaris
        </a>
    </div>

    <!-- Regulatory & Zero PHI Banner -->
    <div style="padding: 12px 16px; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; display: flex; align-items: flex-start; gap: 10px;">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--ml-primary); flex-shrink: 0; margin-top: 1px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div style="font-size: 12.5px; color: var(--ml-text); line-height: 1.5;">
            <strong style="font-weight: 700;">STANDAR KEFARMASIAN &amp; PRIVASI REKAM MEDIS:</strong>
            Pencatatan hanya mengelola atribut logistik sediaan farmasi (SKU, nomor batch, stok fisik, laju pemakaian, dan tanggal kedaluwarsa). Sistem ini <strong>bebas data identitas pasien (Zero PHI)</strong> dan tidak memberikan rekomendasi dosis medis individu.
        </div>
    </div>

    <!-- Form Errors Alert -->
    @if($errors->any())
        <div style="padding: 12px 16px; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 10px; font-size: 12.5px; color: #7F1D1D;">
            <strong style="font-weight: 700; display: block; margin-bottom: 6px;">Terjadi kesalahan pada pengisian formulir:</strong>
            <ul style="list-style: disc; padding-left: 18px; margin: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Input Form Card -->
    <form method="POST" action="{{ route('batches.store') }}" id="batchForm" class="space-y-6">
        @csrf

        @if($user->isSysadmin() && isset($facilities))
            <div class="bg-white p-5 rounded-xl border border-[#DBE5F5] shadow-xs">
                <label for="facility_id" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Fasilitas Kesehatan Penampung Stok:
                </label>
                <select name="facility_id" id="facility_id" class="w-full px-3 py-2 bg-slate-50 border border-[#DBE5F5] rounded-lg text-xs font-semibold text-[#0B1F4B] focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    @foreach($facilities as $f)
                        <option value="{{ $f->id }}" {{ old('facility_id', $user->facility_id) == $f->id ? 'selected' : '' }}>
                            {{ $f->name }} ({{ $f->type }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- STEP 1: Medicine Selection -->
        <div class="bg-white p-6 rounded-xl border border-[#DBE5F5] shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#DBE5F5]">
                <h2 class="text-sm font-bold text-[#0B1F4B] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-[#1D4ED8] text-white flex items-center justify-center text-[11px] font-mono">1</span>
                    Identitas &amp; Jenis Sediaan Obat
                </h2>

                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-[#1D4ED8]">
                    <input type="checkbox" id="toggleNewMedicine" name="is_new_medicine" value="1" {{ old('is_new_medicine') ? 'checked' : '' }} class="rounded border-[#DBE5F5] text-[#1D4ED8] focus:ring-[#1D4ED8]">
                    <span>Obat Belum Terdaftar di Master?</span>
                </label>
            </div>

            <!-- Existing Medicine Picker -->
            <div id="existingMedicineSection" style="{{ old('is_new_medicine') ? 'display: none;' : 'display: block;' }}">
                <label for="medicine_id" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Pilih Obat dari Master Formularium: <span class="text-red-500">*</span>
                </label>
                <select name="medicine_id" id="medicine_id" class="w-full px-3 py-2.5 bg-slate-50 border border-[#DBE5F5] rounded-lg text-xs font-medium text-[#0B1F4B] focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <option value="">-- Pilih Obat yang Tersedia di Sistem --</option>
                    @foreach($medicines as $m)
                        <option value="{{ $m->id }}" 
                            data-price="{{ $m->unit_price_idr }}" 
                            data-unit="{{ $m->unit }}"
                            data-cold="{{ $m->requires_cold_chain ? '1' : '0' }}"
                            {{ old('medicine_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }} ({{ $m->dosage_form }}) — Satuan: {{ $m->unit }} — Rp {{ number_format($m->unit_price_idr, 0, ',', '.') }} {{ $m->requires_cold_chain ? '[Rantai Dingin]' : '' }}
                        </option>
                    @endforeach
                </select>
                <span class="text-[11px] text-[#52627F] block mt-1">
                    Jika nama obat belum tersedia di daftar di atas, centang opsi <em>"Obat Belum Terdaftar di Master?"</em> untuk menambahkan jenis obat baru.
                </span>
            </div>

            <!-- New Medicine Creation Fields -->
            <div id="newMedicineSection" style="{{ old('is_new_medicine') ? 'display: block;' : 'display: none;' }}" class="space-y-4 p-4 bg-slate-50 rounded-lg border border-[#DBE5F5]">
                <div class="text-xs font-bold text-[#0B1F4B] border-b border-slate-200 pb-2">
                    Formulir Registrasi Master Obat Baru
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="new_medicine_name" class="block text-xs font-bold text-[#0B1F4B] mb-1">Nama Dagang Obat: <span class="text-red-500">*</span></label>
                        <input type="text" name="new_medicine_name" id="new_medicine_name" value="{{ old('new_medicine_name') }}" placeholder="Contoh: Ceftriaxone 1g Injeksi" class="w-full px-3 py-2 bg-white border border-[#DBE5F5] rounded-lg text-xs">
                    </div>
                    <div>
                        <label for="new_generic_name" class="block text-xs font-bold text-[#0B1F4B] mb-1">Nama Generik / Zat Aktif: <span class="text-red-500">*</span></label>
                        <input type="text" name="new_generic_name" id="new_generic_name" value="{{ old('new_generic_name') }}" placeholder="Contoh: Ceftriaxone Sodium Serbuk Injeksi" class="w-full px-3 py-2 bg-white border border-[#DBE5F5] rounded-lg text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="new_category" class="block text-xs font-bold text-[#0B1F4B] mb-1">Kategori Terapeutik: <span class="text-red-500">*</span></label>
                        <select name="new_category" id="new_category" class="w-full px-3 py-2 bg-white border border-[#DBE5F5] rounded-lg text-xs">
                            <option value="Antibiotic" {{ old('new_category') == 'Antibiotic' ? 'selected' : '' }}>Antibiotic</option>
                            <option value="Cardiovascular" {{ old('new_category') == 'Cardiovascular' ? 'selected' : '' }}>Cardiovascular</option>
                            <option value="Endocrine" {{ old('new_category') == 'Endocrine' ? 'selected' : '' }}>Endocrine</option>
                            <option value="Analgesic" {{ old('new_category') == 'Analgesic' ? 'selected' : '' }}>Analgesic</option>
                            <option value="Emergency" {{ old('new_category') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="Respiratory" {{ old('new_category') == 'Respiratory' ? 'selected' : '' }}>Respiratory</option>
                            <option value="Other" {{ old('new_category') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label for="new_dosage_form" class="block text-xs font-bold text-[#0B1F4B] mb-1">Bentuk Sediaan: <span class="text-red-500">*</span></label>
                        <input type="text" name="new_dosage_form" id="new_dosage_form" value="{{ old('new_dosage_form') }}" placeholder="Vial, Tablet, Sirup, Infus" class="w-full px-3 py-2 bg-white border border-[#DBE5F5] rounded-lg text-xs">
                    </div>

                    <div>
                        <label for="new_unit" class="block text-xs font-bold text-[#0B1F4B] mb-1">Satuan Kemasan: <span class="text-red-500">*</span></label>
                        <input type="text" name="new_unit" id="new_unit" value="{{ old('new_unit', 'Box') }}" placeholder="Box, Vial, Botol, Ampul" class="w-full px-3 py-2 bg-white border border-[#DBE5F5] rounded-lg text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_unit_price_idr" class="block text-xs font-bold text-[#0B1F4B] mb-1">Harga Acuan Satuan (Rp): <span class="text-red-500">*</span></label>
                        <input type="number" name="new_unit_price_idr" id="new_unit_price_idr" value="{{ old('new_unit_price_idr', 50000) }}" min="0" class="w-full px-3 py-2 bg-white border border-[#DBE5F5] rounded-lg text-xs font-mono">
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-[#0B1F4B]">
                            <input type="checkbox" name="new_requires_cold_chain" id="new_requires_cold_chain" value="1" {{ old('new_requires_cold_chain') ? 'checked' : '' }} class="rounded border-[#DBE5F5] text-[#1D4ED8]">
                            <span>Wajib Rantai Dingin / Cold Chain (2°C - 8°C)?</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="new_handling_instructions" class="block text-xs font-bold text-[#0B1F4B] mb-1">Petunjuk Khusus Penyimpanan (Opsional):</label>
                    <input type="text" name="new_handling_instructions" id="new_handling_instructions" value="{{ old('new_handling_instructions') }}" placeholder="Simpan pada suhu ruang kering di bawah 25°C." class="w-full px-3 py-2 bg-white border border-[#DBE5F5] rounded-lg text-xs">
                </div>
            </div>
        </div>

        <!-- STEP 2: Batch Ledger Details -->
        <div class="bg-white p-6 rounded-xl border border-[#DBE5F5] shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-[#0B1F4B] uppercase tracking-wider pb-3 border-b border-[#DBE5F5] flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-[#1D4ED8] text-white flex items-center justify-center text-[11px] font-mono">2</span>
                Informasi Batch, Stok &amp; Kedaluwarsa
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Batch Number -->
                <div>
                    <label for="batch_number" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                        Nomor Batch / Lot Pabrik: <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number') }}" placeholder="Contoh: CFT-2026-X89" required class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono font-bold uppercase focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <span class="text-[11px] text-[#52627F] block mt-1">Sesuai yang tertera pada kemasan fisik obat.</span>
                </div>

                <!-- Expiry Date -->
                <div>
                    <label for="expiry_date" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                        Tanggal Kedaluwarsa (Expired Date): <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" required class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-semibold focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <span class="text-[11px] text-[#52627F] block mt-1">Tanggal berakhir masa edar obat.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Current Stock -->
                <div>
                    <label for="current_stock" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                        Jumlah Stok Fisik Aktif: <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="current_stock" id="current_stock" value="{{ old('current_stock', 100) }}" min="1" required class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono font-bold focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <span class="text-[11px] text-[#52627F] block mt-1">Stok yang ada di gudang saat ini.</span>
                </div>

                <!-- Daily Burn Rate -->
                <div>
                    <label for="daily_burn_rate" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                        Laju Pakai Harian (Burn Rate): <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.1" name="daily_burn_rate" id="daily_burn_rate" value="{{ old('daily_burn_rate', 2.0) }}" min="0" required class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono font-bold focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <span class="text-[11px] text-[#52627F] block mt-1">Rata-rata resep/unit per hari di faskes.</span>
                </div>

                <!-- Unit Price Override -->
                <div>
                    <label for="unit_price_idr" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                        Harga Satuan Batch (Rp):
                    </label>
                    <input type="number" name="unit_price_idr" id="unit_price_idr" value="{{ old('unit_price_idr') }}" placeholder="Gunakan harga master jika kosong" min="0" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <span class="text-[11px] text-[#52627F] block mt-1">Kosongkan jika sama dengan harga master.</span>
                </div>
            </div>

            <!-- Storage Location -->
            <div>
                <label for="storage_location" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Lokasi Fisik di Apotek/Gudang (Opsional):
                </label>
                <input type="text" name="storage_location" id="storage_location" value="{{ old('storage_location') }}" placeholder="Contoh: Gudang Farmasi - Rak Injeksi B-02" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
            </div>
        </div>

        <!-- STEP 3: Real-Time Engine Forecast Preview Box -->
        <div class="bg-gradient-to-r from-slate-900 to-blue-950 text-white p-5 rounded-xl border border-slate-800 shadow-md">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <span class="text-xs font-bold tracking-wider text-blue-300 uppercase">
                    Prediksi Risiko Klinis &amp; Estimasi Limbah (Otomatis)
                </span>
                <span class="text-[10px] font-mono text-slate-400">Algoritma Redistribusi MediLoop</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-xs font-mono">
                <div>
                    <span class="text-slate-400 block text-[11px]">Sisa Hari Expiry:</span>
                    <span id="previewDaysExpiry" class="text-base font-bold text-white mt-0.5 block">- hari</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Hari Hingga Habis:</span>
                    <span id="previewDaysDeplete" class="text-base font-bold text-white mt-0.5 block">- hari</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Potensi Terbuang:</span>
                    <span id="previewWaste" class="text-base font-bold text-amber-300 mt-0.5 block">- unit</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Klasifikasi Risiko:</span>
                    <span id="previewRisk" class="mt-0.5 inline-block px-2 py-0.5 rounded text-[11px] font-black bg-slate-800 text-slate-300">
                        MEMERIKSA...
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Submit & Action Bar -->
        <div class="pt-2 flex items-center justify-between">
            <a href="{{ route('batches.index') }}" class="px-4 py-2 text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B]">
                Batal
            </a>

            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-md transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Data Stok Batch</span>
            </button>
        </div>
    </form>

</div>

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

        // Toggle existing vs new medicine
        toggleNewMedicine.addEventListener('change', function () {
            if (this.checked) {
                existingSection.style.display = 'none';
                newSection.style.display = 'block';
                medicineSelect.removeAttribute('required');
            } else {
                existingSection.style.display = 'block';
                newSection.style.display = 'none';
                medicineSelect.setAttribute('required', 'required');
            }
        });

        // Set default expiry date to 60 days from now if empty
        if (!expiryDateInput.value) {
            const defaultDate = new Date();
            defaultDate.setDate(defaultDate.getDate() + 60);
            expiryDateInput.value = defaultDate.toISOString().split('T')[0];
        }

        // Auto-fill price on medicine select
        medicineSelect.addEventListener('change', function () {
            const selected = medicineSelect.options[medicineSelect.selectedIndex];
            if (selected && selected.dataset.price && !unitPriceInput.value) {
                unitPriceInput.value = selected.dataset.price;
            }
        });

        // Dynamic recalculation
        function updatePreview() {
            const stock = parseInt(currentStockInput.value) || 0;
            const burnRate = parseFloat(dailyBurnRateInput.value) || 0;
            const expiryVal = expiryDateInput.value;

            if (!expiryVal) return;

            const expiryDate = new Date(expiryVal);
            const today = new Date();
            today.setHours(0,0,0,0);

            const diffTime = expiryDate.getTime() - today.getTime();
            const daysToExpiry = Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));

            const daysToDeplete = burnRate > 0 ? (stock / burnRate).toFixed(1) : 'Tak Hingga';
            const expectedConsumption = Math.floor(burnRate * daysToExpiry);
            const projectedWaste = Math.max(0, stock - expectedConsumption);
            const wasteRatio = stock > 0 ? (projectedWaste / stock) : 0;

            previewDaysExpiry.textContent = `${daysToExpiry} hari`;
            previewDaysDeplete.textContent = burnRate > 0 ? `${daysToDeplete} hari` : '∞ (0/hari)';
            previewWaste.textContent = `${projectedWaste} unit (${(wasteRatio * 100).toFixed(0)}%)`;

            // Determine Risk Level matching PRD
            if (projectedWaste <= 0) {
                previewRisk.textContent = 'RENDAH (STOK AMAN)';
                previewRisk.className = 'mt-0.5 inline-block px-2 py-0.5 rounded text-[11px] font-black bg-emerald-900 text-emerald-200 border border-emerald-700';
            } else if (daysToExpiry <= 30) {
                previewRisk.textContent = 'KRITIS (SEGERA REDISTRIBUSI)';
                previewRisk.className = 'mt-0.5 inline-block px-2 py-0.5 rounded text-[11px] font-black bg-red-900 text-red-200 border border-red-700';
            } else if (daysToExpiry <= 60 || wasteRatio >= 0.25) {
                previewRisk.textContent = 'TINGGI (REKOMENDASI TRANSFER)';
                previewRisk.className = 'mt-0.5 inline-block px-2 py-0.5 rounded text-[11px] font-black bg-orange-900 text-orange-200 border border-orange-700';
            } else {
                previewRisk.textContent = 'SEDANG (PERHATIAN)';
                previewRisk.className = 'mt-0.5 inline-block px-2 py-0.5 rounded text-[11px] font-black bg-amber-900 text-amber-200 border border-amber-700';
            }
        }

        currentStockInput.addEventListener('input', updatePreview);
        dailyBurnRateInput.addEventListener('input', updatePreview);
        expiryDateInput.addEventListener('change', updatePreview);

        updatePreview();
    });
</script>
@endsection
