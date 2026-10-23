@extends('layouts.mediloop', ['title' => 'Edit Batch #'.$batch->batch_number])

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-[#52627F]">
        <a href="{{ route('dashboard') }}" class="hover:underline text-[#1D4ED8]">Dashboard</a>
        <span>/</span>
        <a href="{{ route('batches.index') }}" class="hover:underline text-[#1D4ED8]">Inventaris Obat</a>
        <span>/</span>
        <span class="text-[#0B1F4B] font-semibold">Edit Batch #{{ $batch->batch_number }}</span>
    </div>

    <!-- Header -->
    <div class="pb-4 border-b border-[#DBE5F5] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B1F4B]">Edit Data Batch Stok</h1>
            <p class="text-xs text-[#52627F] mt-1">
                Perbarui jumlah fisik, laju pemakaian, atau tanggal masa simpan batch obat.
            </p>
        </div>
        <a href="{{ route('batches.index') }}" class="text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] px-3 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg border border-[#DBE5F5] transition">
            &larr; Kembali
        </a>
    </div>

    <!-- Medicine Summary Card -->
    <div class="bg-white p-5 rounded-xl border border-[#DBE5F5] shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-mono text-[#52627F]">SEDIAAN FARMASI</span>
            <div class="text-lg font-bold text-[#0B1F4B]">{{ $batch->medicine->name }}</div>
            <div class="text-xs text-[#52627F]">
                {{ $batch->medicine->dosage_form }} • Satuan: {{ $batch->medicine->unit }} • Faskes: {{ $batch->facility->name }}
            </div>
        </div>
        <div class="text-right">
            <span class="px-3 py-1 font-mono font-bold text-xs rounded bg-slate-100 text-[#0B1F4B] border border-slate-200">
                BATCH: {{ $batch->batch_number }}
            </span>
        </div>
    </div>

    <!-- Form Errors Alert -->
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
            <strong class="font-bold block mb-1">Terjadi kesalahan:</strong>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form Card -->
    <form method="POST" action="{{ route('batches.update', $batch) }}" class="bg-white p-6 rounded-xl border border-[#DBE5F5] shadow-xs space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Current Stock -->
            <div>
                <label for="current_stock" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Jumlah Stok Fisik Aktif: <span class="text-red-500">*</span>
                </label>
                <input type="number" name="current_stock" id="current_stock" value="{{ old('current_stock', $batch->current_stock) }}" min="0" required class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono font-bold focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
            </div>

            <!-- Daily Burn Rate -->
            <div>
                <label for="daily_burn_rate" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Laju Pakai Harian (Burn Rate): <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.1" name="daily_burn_rate" id="daily_burn_rate" value="{{ old('daily_burn_rate', $batch->daily_burn_rate) }}" min="0" required class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono font-bold focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Expiry Date -->
            <div>
                <label for="expiry_date" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Tanggal Kedaluwarsa: <span class="text-red-500">*</span>
                </label>
                <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $batch->expiry_date->toDateString()) }}" required class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-semibold focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
            </div>

            <!-- Unit Price -->
            <div>
                <label for="unit_price_idr" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Harga Satuan (IDR): <span class="text-red-500">*</span>
                </label>
                <input type="number" name="unit_price_idr" id="unit_price_idr" value="{{ old('unit_price_idr', $batch->unit_price_idr) }}" min="0" required class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Storage Location -->
            <div>
                <label for="storage_location" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Lokasi Fisik di Rak/Chiller:
                </label>
                <input type="text" name="storage_location" id="storage_location" value="{{ old('storage_location', $batch->storage_location) }}" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-xs font-bold text-[#0B1F4B] mb-1">
                    Status Siklus Batch: <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-semibold focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <option value="ACTIVE" {{ old('status', $batch->status) === 'ACTIVE' ? 'selected' : '' }}>ACTIVE (Tersedia)</option>
                    <option value="RESERVED" {{ old('status', $batch->status) === 'RESERVED' ? 'selected' : '' }}>RESERVED (Dialokasikan)</option>
                    <option value="DEPLETED" {{ old('status', $batch->status) === 'DEPLETED' ? 'selected' : '' }}>DEPLETED (Habis)</option>
                    <option value="EXPIRED" {{ old('status', $batch->status) === 'EXPIRED' ? 'selected' : '' }}>EXPIRED (Kedaluwarsa)</option>
                </select>
            </div>
        </div>

        <div class="pt-4 border-t border-[#DBE5F5] flex items-center justify-between">
            <a href="{{ route('batches.index') }}" class="text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B]">
                Batal
            </a>

            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>

</div>
@endsection
