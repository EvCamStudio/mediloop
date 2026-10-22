@extends('layouts.mediloop', ['title' => 'Inventaris & Data Stok Obat — MediLoop'])

@section('content')
<div class="space-y-6">

    <!-- Flash Alerts -->
    @if(session('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-3 text-emerald-900 text-xs font-semibold">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if($errors->has('delete_error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-red-900 text-xs font-semibold">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first('delete_error') }}</span>
        </div>
    @endif

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#DBE5F5] gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#0B1F4B]">Inventaris &amp; Data Stok Batch Obat</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-blue-100 text-[#1D4ED8]">
                    {{ $facility?->name ?? 'Fasilitas Terhubung' }}
                </span>
            </div>
            <p class="text-xs text-[#52627F] mt-1">
                Kelola dan input stok aktif faskes untuk pelacakan masa kedaluwarsa serta rekomendasi redistribusi surplus.
            </p>
        </div>

        @if(! $user->isDinkesAuditor())
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('ml-csv-modal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-[#1D4ED8] bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span>Import Stok (CSV)</span>
                </button>
                <a href="{{ route('batches.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Input Stok Manual</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-[#DBE5F5] shadow-xs">
        <form method="GET" action="{{ route('batches.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            @if(($user->isDinkesAuditor() || $user->role === 'SYSADMIN') && isset($facilitiesList))
                <div class="md:col-span-3">
                    <select name="facility_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-[#DBE5F5] rounded-lg text-xs font-medium text-[#0B1F4B] focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                        <option value="">Semua Fasilitas Kesehatan</option>
                        @foreach($facilitiesList as $f)
                            <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="{{ ($user->isDinkesAuditor() || $user->role === 'SYSADMIN') ? 'md:col-span-6' : 'md:col-span-9' }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ $currentSearch }}" placeholder="Cari nama obat, kandungan generik, nomor batch, atau lokasi rak..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-[#DBE5F5] rounded-lg text-xs font-medium text-[#0B1F4B] focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="md:col-span-2">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-[#DBE5F5] rounded-lg text-xs font-medium text-[#0B1F4B] focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <option value="">Status: Semua</option>
                    <option value="ACTIVE" {{ $currentStatus === 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                    <option value="RESERVED" {{ $currentStatus === 'RESERVED' ? 'selected' : '' }}>RESERVED</option>
                    <option value="DEPLETED" {{ $currentStatus === 'DEPLETED' ? 'selected' : '' }}>DEPLETED</option>
                    <option value="EXPIRED" {{ $currentStatus === 'EXPIRED' ? 'selected' : '' }}>EXPIRED</option>
                </select>
            </div>

            <div class="md:col-span-1 flex gap-2">
                <button type="submit" class="w-full px-3 py-2 bg-slate-100 hover:bg-slate-200 text-[#0B1F4B] text-xs font-semibold rounded-lg border border-[#DBE5F5] transition text-center">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Inventory Batches Table -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-[#DBE5F5] text-[#52627F] uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Nama Obat &amp; Bentuk Sediaan</th>
                        <th class="py-3.5 px-4 font-mono">No. Batch</th>
                        <th class="py-3.5 px-4 text-right">Stok Aktif</th>
                        <th class="py-3.5 px-4 text-center">Laju Pakai / Hari</th>
                        <th class="py-3.5 px-4 text-center">Sisa Waktu vs Habis Stok</th>
                        <th class="py-3.5 px-4 text-right font-mono">Proyeksi Limbah</th>
                        <th class="py-3.5 px-4 text-center">Tingkat Risiko</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DBE5F5]">
                    @forelse($batches as $b)
                        @php
                            $risk = $b->risk_level;
                            $daysToExpiry = $b->days_to_expiry;
                            $daysToDeplete = $b->days_to_deplete;
                            $projectedWaste = $b->projected_waste;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Medicine Info -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#0B1F4B] text-[13px]">
                                    {{ $b->medicine->name }}
                                </div>
                                <div class="text-[11px] text-[#52627F] flex items-center gap-2 mt-0.5">
                                    <span>{{ $b->medicine->dosage_form }}</span>
                                    <span>•</span>
                                    <span>{{ $b->medicine->category }}</span>
                                    @if($b->medicine->requires_cold_chain)
                                        <span class="px-1.5 py-0.2 rounded bg-cyan-100 text-cyan-800 text-[10px] font-bold">Rantai Dingin</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Lokasi: {{ $b->storage_location ?? 'Gudang Farmasi' }}
                                </div>
                            </td>

                            <!-- Batch Number -->
                            <td class="py-3.5 px-4 font-mono font-semibold text-[#0B1F4B]">
                                <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    {{ $b->batch_number }}
                                </span>
                                <span class="block text-[10px] text-[#52627F] mt-1">
                                    Exp: {{ $b->expiry_date->format('d/m/Y') }}
                                </span>
                            </td>

                            <!-- Stock -->
                            <td class="py-3.5 px-4 text-right">
                                <span class="font-extrabold text-[#0B1F4B] text-sm">
                                    {{ number_format($b->current_stock) }}
                                </span>
                                <span class="text-[11px] text-[#52627F] block">
                                    {{ $b->medicine->unit }}
                                </span>
                            </td>

                            <!-- Burn Rate -->
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-[#0B1F4B]">
                                {{ $b->daily_burn_rate }}
                                <span class="text-[10px] font-normal text-[#52627F] block">unit/hari</span>
                            </td>

                            <!-- Days comparison -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5 text-[11px]">
                                    <span class="px-2 py-0.5 rounded font-bold {{ $daysToExpiry <= 45 ? 'bg-orange-100 text-[#D9480F]' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $daysToExpiry }}h Exp
                                    </span>
                                    <span class="text-slate-400">vs</span>
                                    <span class="px-2 py-0.5 rounded font-bold bg-blue-50 text-[#1D4ED8]">
                                        {{ is_finite($daysToDeplete) ? $daysToDeplete.'h' : '∞' }} Habis
                                    </span>
                                </div>
                            </td>

                            <!-- Projected Waste -->
                            <td class="py-3.5 px-4 text-right font-mono">
                                @if($projectedWaste > 0)
                                    <span class="font-extrabold text-[#D9480F] text-xs">
                                        {{ number_format($projectedWaste) }} unit
                                    </span>
                                    <span class="block text-[10px] text-slate-500">
                                        Rp {{ number_format($projectedWaste * (float) $b->unit_price_idr, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-emerald-600 font-bold text-xs">0 (Aman)</span>
                                @endif
                            </td>

                            <!-- Risk Badge -->
                            <td class="py-3.5 px-4 text-center">
                                @if($risk === 'CRITICAL')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-red-100 text-red-800 border border-red-300">
                                        KRITIS
                                    </span>
                                @elseif($risk === 'HIGH')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-orange-100 text-[#D9480F] border border-orange-300">
                                        TINGGI
                                    </span>
                                @elseif($risk === 'MEDIUM')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                        SEDANG
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        RENDAH
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($projectedWaste > 0 && $b->current_stock > 0 && ! $user->isDinkesAuditor())
                                        <a href="{{ route('transfers.propose', $b) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded text-white bg-[#1D4ED8] hover:bg-blue-800 transition">
                                            <span>Usulkan</span>
                                        </a>
                                    @endif

                                    @if(! $user->isDinkesAuditor())
                                        <a href="{{ route('batches.edit', $b) }}" class="px-2 py-1 text-xs font-medium rounded text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('batches.destroy', $b) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus batch {{ $b->batch_number }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-xs font-medium rounded text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-[#52627F]">
                                <div class="max-w-md mx-auto">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="font-bold text-[#0B1F4B] text-sm">Tidak ada data batch obat</p>
                                    <p class="text-xs text-slate-500 mt-1 mb-4">
                                        Belum ada batch obat yang sesuai dengan filter pencarian ini. Tambahkan stok batch obat baru ke inventaris faskes Anda.
                                    </p>
                                    @if(! $user->isDinkesAuditor())
                                        <a href="{{ route('batches.create') }}" class="inline-flex items-center gap-1 px-4 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                                            Input Stok Obat Baru
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
            <div class="p-4 border-t border-[#DBE5F5]">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    <!-- CSV Import Modal -->
    <div id="ml-csv-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-[#0B1F4B]/60 backdrop-blur-xs transition-opacity" onclick="document.getElementById('ml-csv-modal').classList.add('hidden')"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-[#DBE5F5]">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-900 to-[#1D4ED8] p-5 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold" id="modal-title">Import Stok Batch via CSV</h3>
                            <p class="text-xs text-blue-100">Unggah laporan inventaris obat dari SIMRS / SIMPUS</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('ml-csv-modal').classList.add('hidden')" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('batches.importCsv') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf

                    <!-- Instructions & Template Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3.5 flex items-start justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <p class="font-bold text-[#1D4ED8]">Format Kolom CSV yang Didukung:</p>
                            <p class="text-slate-600 leading-relaxed text-[11.5px]">
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">nama_obat</code>, 
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">nomor_batch</code>, 
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">stok_saat_ini</code>, 
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">tanggal_kadaluwarsa</code>, 
                                <code class="bg-white px-1.5 py-0.5 rounded text-blue-900 font-mono">laju_pemakaian_harian</code>
                            </p>
                        </div>
                        <a href="{{ route('batches.csvTemplate') }}" class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-blue-100/60 border border-blue-300 text-[#1D4ED8] font-bold rounded-lg text-[11px] shadow-2xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Template</span>
                        </a>
                    </div>

                    <!-- Dropzone File Input -->
                    <div class="border-2 border-dashed border-[#BFDBFE] hover:border-[#1D4ED8] bg-slate-50/70 hover:bg-blue-50/40 rounded-xl p-6 text-center transition cursor-pointer" onclick="document.getElementById('csv_file_input').click()">
                        <svg class="w-10 h-10 mx-auto text-[#1D4ED8] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-xs font-bold text-[#0B1F4B]">Pilih file CSV dari komputer Anda</p>
                        <p class="text-[11px] text-[#52627F] mt-0.5">Mendukung file .csv atau .txt (Maksimal 5 MB)</p>
                        <p id="selected-file-name" class="mt-2 text-xs font-semibold text-emerald-700 hidden"></p>
                        <input type="file" name="csv_file" id="csv_file_input" accept=".csv,.txt" required class="hidden" onchange="if(this.files[0]) { document.getElementById('selected-file-name').textContent = 'File terpilih: ' + this.files[0].name; document.getElementById('selected-file-name').classList.remove('hidden'); }">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('ml-csv-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-[#52627F] hover:bg-slate-100 rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Mulai Impor &amp; Kalkulasi</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>
@endsection
