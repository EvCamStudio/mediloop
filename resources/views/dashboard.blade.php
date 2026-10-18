@extends('layouts.mediloop', ['title' => 'Dashboard Stok & Risiko — MediLoop'])

@section('content')
<div class="space-y-6">

    <!-- Facility Overview Header -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-[#1D4ED8] uppercase tracking-wider">
                    {{ $facility ? $facility->type : 'Pengawasan Regional' }}
                </span>
                <span class="text-[#52627F]">•</span>
                <span class="text-xs text-[#52627F] font-mono">Kode: {{ $facility ? $facility->code : 'ALL-REGION' }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B1F4B]">
                {{ $facility ? $facility->name : 'Dashboard Pengawasan Faskes' }}
            </h1>
            <p class="text-xs text-[#52627F] mt-1">
                {{ $facility ? $facility->address : 'Monitoring seluruh faskes se-wilayah DKI Jakarta' }}
            </p>
        </div>

        @if($user->isDinkesAuditor())
        <div class="flex items-center gap-2">
            <label for="filterFacility" class="text-xs font-semibold text-[#52627F]">Filter Faskes:</label>
            <form method="GET" action="{{ route('dashboard') }}">
                <select id="filterFacility" name="facility_id" onchange="this.form.submit()" class="text-xs font-medium border border-[#DBE5F5] rounded-lg px-3 py-2 bg-slate-50 focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                    <option value="">Semua Faskes Regional</option>
                    @foreach($facilitiesList as $f)
                        <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->name }} ({{ $f->type }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
    </div>

    <!-- 4 Key Metric Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Stock -->
        <div class="p-5 bg-white rounded-xl border border-[#DBE5F5] shadow-xs">
            <span class="text-xs font-medium text-[#52627F] block">Total Stok Fisik Aktif</span>
            <span class="text-2xl font-extrabold font-mono text-[#0B1F4B] mt-1 block">
                {{ number_format($totalStockUnits) }} <span class="text-xs font-normal text-[#52627F]">unit</span>
            </span>
            <span class="text-[11px] text-[#52627F] mt-2 block">Terbagi dalam {{ $batches->count() }} batch</span>
        </div>

        <!-- Metric 2: At-Risk Batches -->
        <div class="p-5 bg-white rounded-xl border border-[#DBE5F5] shadow-xs">
            <span class="text-xs font-medium text-[#52627F] block">Batch Berisiko Kedaluwarsa</span>
            <span class="text-2xl font-extrabold font-mono text-[#D9480F] mt-1 block">
                {{ $atRiskCount }} <span class="text-xs font-normal text-[#52627F]">batch</span>
            </span>
            <span class="text-[11px] text-[#D9480F] font-semibold mt-2 block">Kategori Kritis & Tinggi</span>
        </div>

        <!-- Metric 3: Projected Waste Units -->
        <div class="p-5 bg-white rounded-xl border border-[#DBE5F5] shadow-xs">
            <span class="text-xs font-medium text-[#52627F] block">Proyeksi Limbah (Tak Terserap)</span>
            <span class="text-2xl font-extrabold font-mono text-[#D9480F] mt-1 block">
                {{ number_format($totalProjectedWasteUnits) }} <span class="text-xs font-normal text-[#52627F]">unit</span>
            </span>
            <span class="text-[11px] text-[#52627F] mt-2 block">Jika tidak diredistribusikan</span>
        </div>

        <!-- Metric 4: Estimated Economic Value at Risk -->
        <div class="p-5 bg-white rounded-xl border border-[#DBE5F5] shadow-xs">
            <span class="text-xs font-medium text-[#52627F] block">Nilai Finansial Terancam</span>
            <span class="text-2xl font-extrabold font-mono text-[#0B1F4B] mt-1 block">
                Rp {{ number_format($totalProjectedWasteValue, 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-[#1D4ED8] font-semibold mt-2 block">Dapat diselamatkan via redistribusi</span>
        </div>
    </div>

    <!-- Inventory Batch Ledger -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#DBE5F5] flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-[#0B1F4B]">Buku Besar Persediaan & Analisis Risiko Expiry</h2>
                <p class="text-xs text-[#52627F]">Data kalkulasi diperbarui real-time berdasarkan burn rate observasi harian.</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-mono font-medium text-[#52627F] bg-slate-100 rounded border border-[#DBE5F5]">
                {{ $batches->count() }} Data Batch
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-[#DBE5F5] text-[#52627F] font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Nama Obat & Sediaan</th>
                        <th class="py-3.5 px-4 font-mono">No. Batch</th>
                        <th class="py-3.5 px-4 text-right font-mono">Stok</th>
                        <th class="py-3.5 px-4 text-right font-mono">Laju Pakai</th>
                        <th class="py-3.5 px-4 text-right font-mono">Sisa Waktu</th>
                        <th class="py-3.5 px-4 text-right font-mono">Proyeksi Limbah</th>
                        <th class="py-3.5 px-4">Evaluasi Risiko</th>
                        <th class="py-3.5 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DBE5F5]">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-[#EAF1FF]/30 transition {{ $batch->risk_level === 'CRITICAL' ? 'bg-red-50/30' : ($batch->risk_level === 'HIGH' ? 'bg-orange-50/20' : '') }}">
                            <!-- Medicine Info -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-[#0B1F4B]">{{ $batch->medicine->name }}</div>
                                <div class="text-[11px] text-[#52627F] flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $batch->medicine->category }}</span>
                                    <span>•</span>
                                    <span>{{ $batch->medicine->dosage_form }}</span>
                                    @if($batch->medicine->requires_cold_chain)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">
                                            Cold-Chain (2-8°C)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Batch Number & Storage Location -->
                            <td class="py-3 px-4 font-mono">
                                <span class="font-semibold text-[#0B1F4B]">{{ $batch->batch_number }}</span>
                                <span class="block text-[10px] text-[#52627F]">{{ $batch->storage_location ?? 'Gudang Farmasi' }}</span>
                            </td>

                            <!-- Current Stock -->
                            <td class="py-3 px-4 text-right font-mono font-bold text-[#0B1F4B]">
                                {{ number_format($batch->current_stock) }}
                                <span class="text-[10px] text-[#52627F] font-normal block">{{ $batch->medicine->unit }}</span>
                            </td>

                            <!-- Daily Burn Rate -->
                            <td class="py-3 px-4 text-right font-mono">
                                <span class="font-bold text-[#0B1F4B]">{{ number_format($batch->daily_burn_rate, 1) }}</span>
                                <span class="text-[10px] text-[#52627F] block">unit/hari</span>
                            </td>

                            <!-- Days to Expiry & Date -->
                            <td class="py-3 px-4 text-right font-mono">
                                <span class="font-bold {{ $batch->days_to_expiry <= 30 ? 'text-[#D9480F]' : 'text-[#0B1F4B]' }}">
                                    {{ $batch->days_to_expiry }} hari
                                </span>
                                <span class="text-[10px] text-[#52627F] block">{{ $batch->expiry_date->format('d M Y') }}</span>
                            </td>

                            <!-- Projected Waste -->
                            <td class="py-3 px-4 text-right font-mono">
                                @if($batch->projected_waste > 0)
                                    <span class="font-bold text-[#D9480F]">
                                        {{ number_format($batch->projected_waste) }} unit
                                    </span>
                                    <span class="text-[10px] text-[#D9480F] block">({{ round($batch->waste_ratio * 100, 1) }}%)</span>
                                @else
                                    <span class="font-semibold text-slate-600">0 unit</span>
                                    <span class="text-[10px] text-slate-500 block">Habis terpakai</span>
                                @endif
                            </td>

                            <!-- Risk Level Badge (Never Color Alone, Always Explicit Text) -->
                            <td class="py-3 px-4">
                                @if($batch->risk_level === 'CRITICAL')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-[#D9480F] border border-red-300">
                                        [KRITIS: ≤ 30 HARI]
                                    </span>
                                @elseif($batch->risk_level === 'HIGH')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-orange-100 text-[#D9480F] border border-orange-300">
                                        [RISIKO TINGGI: {{ $batch->projected_waste }} UNIT]
                                    </span>
                                @elseif($batch->risk_level === 'MEDIUM')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-[#B7791F] border border-amber-300">
                                        [MEDIUM: PANTAU]
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-300">
                                        [AMAN: KONSUMSI CUKUP]
                                    </span>
                                @endif
                            </td>

                            <!-- Action Column -->
                            <td class="py-3 px-4 text-right">
                                @if($batch->projected_waste > 0)
                                    <a href="{{ route('transfers.propose', $batch) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-md text-white bg-[#1D4ED8] hover:bg-blue-800 transition shadow-xs">
                                        <span>Redistribusi</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-600 font-medium italic">Tidak Perlu Transfer</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-[#52627F]">
                                Tidak ada data batch aktif pada fasilitas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
