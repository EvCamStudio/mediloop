@extends('layouts.mediloop', ['title' => 'Audit Regional Dinkes'])

@section('content')
<div class="space-y-6">

    <!-- Header & Regional Overview -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-[#1D4ED8] uppercase tracking-wider">PORTAL PENGAWASAN REGIONAL</span>
                <span class="text-[#52627F]">•</span>
                <span class="text-xs text-[#52627F] font-mono">DINAS KESEHATAN PROVINSI DKI JAKARTA</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B1F4B]">Pengawasan Logistik & Audit Redistribusi Obat</h1>
            <p class="text-xs text-[#52627F] mt-1">
                Audit trail transparansi pencegahan limbah kefarmasian dan kepatuhan distribusi antarfaskes.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 text-xs font-bold text-[#1D4ED8] bg-[#EAF1FF] rounded-lg border border-blue-200">
                Status Sistem: 100% Kepatuhan Audit
            </span>
        </div>
    </div>

    <!-- 4 High-Level Impact Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Prevented Waste Units -->
        <div class="p-5 bg-white rounded-xl border border-[#DBE5F5] shadow-xs">
            <span class="text-xs font-medium text-[#52627F] block">Total Obat Berhasil Diselamatkan</span>
            <span class="text-2xl font-extrabold font-mono text-[#1D4ED8] mt-1 block">
                {{ number_format($totalUnitsSaved) }} <span class="text-xs font-normal text-[#52627F]">unit</span>
            </span>
            <span class="text-[11px] text-[#52627F] mt-2 block">Dari pemusnahan kedaluwarsa</span>
        </div>

        <!-- Metric 2: Prevented Economic Waste in Rupiah -->
        <div class="p-5 bg-white rounded-xl border border-[#DBE5F5] shadow-xs">
            <span class="text-xs font-medium text-[#52627F] block">Nilai Finansial Terselamatkan</span>
            <span class="text-2xl font-extrabold font-mono text-[#0B1F4B] mt-1 block">
                Rp {{ number_format($totalValueSavedIdr, 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-[#1D4ED8] font-semibold mt-2 block">Efisiensi anggaran faskes</span>
        </div>

        <!-- Metric 3: Monitored Facilities -->
        <div class="p-5 bg-white rounded-xl border border-[#DBE5F5] shadow-xs">
            <span class="text-xs font-medium text-[#52627F] block">Fasilitas Kesehatan Terhubung</span>
            <span class="text-2xl font-extrabold font-mono text-[#0B1F4B] mt-1 block">
                {{ $facilitiesCount }} <span class="text-xs font-normal text-[#52627F]">faskes</span>
            </span>
            <span class="text-[11px] text-[#52627F] mt-2 block">RSUD, Puskesmas, Klinik</span>
        </div>

        <!-- Metric 4: Total Active Batches in Region -->
        <div class="p-5 bg-white rounded-xl border border-[#DBE5F5] shadow-xs">
            <span class="text-xs font-medium text-[#52627F] block">Batch Farmasi Terpantau</span>
            <span class="text-2xl font-extrabold font-mono text-[#0B1F4B] mt-1 block">
                {{ $totalActiveBatches }} <span class="text-xs font-normal text-[#52627F]">batch</span>
            </span>
            <span class="text-[11px] text-[#52627F] mt-2 block">Pelacakan masa aktif real-time</span>
        </div>
    </div>

    <!-- Completed Rebalances Table -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#DBE5F5] flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-[#0B1F4B]">Rekap Transaksi Redistribusi Berhasil</h2>
                <p class="text-xs text-[#52627F]">Transfer yang telah terkonfirmasi fisik dan selesai mutasi stok secara atomik.</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-mono font-medium text-[#1D4ED8] bg-[#EAF1FF] rounded border border-blue-200">
                {{ $completedTransfers->count() }} Selesai
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-[#DBE5F5] text-[#52627F] uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 font-mono">Kode Transfer</th>
                        <th class="py-3 px-4">Nama Obat & Batch</th>
                        <th class="py-3 px-4">Donor &rarr; Penerima</th>
                        <th class="py-3 px-4 text-right font-mono">Jumlah</th>
                        <th class="py-3 px-4 text-right font-mono">Nilai (IDR)</th>
                        <th class="py-3 px-4">Kurir & Surat Jalan</th>
                        <th class="py-3 px-4 text-right font-mono">Waktu Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DBE5F5]">
                    @forelse($completedTransfers as $t)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#0B1F4B]">
                                <a href="{{ route('transfers.show', $t) }}" class="text-[#1D4ED8] hover:underline">
                                    {{ $t->transfer_code }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-[#0B1F4B]">{{ $t->batch->medicine->name }}</span>
                                <span class="block text-[11px] font-mono text-[#52627F]">Batch: {{ $t->batch->batch_number }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-[#0B1F4B]">{{ $t->donorFacility->name }}</div>
                                <div class="text-[11px] text-[#1D4ED8]">&rarr; {{ $t->recipientFacility->name }} ({{ $t->distance_km }} km)</div>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-[#0B1F4B]">
                                {{ number_format($t->quantity) }} {{ $t->batch->medicine->unit }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-[#0B1F4B]">
                                Rp {{ number_format($t->quantity * (float) $t->batch->unit_price_idr, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-xs text-[#52627F]">
                                <div>{{ $t->courier_type ?? 'Kurir Khusus Farmasi' }}</div>
                                <div class="font-mono text-[10px] text-slate-500">Resi: {{ $t->tracking_number ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-[#52627F]">
                                {{ $t->completed_at ? $t->completed_at->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-[#52627F]">
                                Belum ada transfer selesai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Immutable Audit Trail Table -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#DBE5F5] flex items-center justify-between bg-slate-50">
            <div>
                <h2 class="text-base font-bold text-[#0B1F4B]">Jejak Audit Sistem (Immutable Audit Logs)</h2>
                <p class="text-xs text-[#52627F]">Catatan permanen setiap tindakan perubahan status atau mutasi inventaris.</p>
            </div>
            <span class="text-xs font-mono text-[#52627F]">Non-Repudiation Security Standard</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-[#DBE5F5] text-[#52627F] uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 font-mono">Waktu (WIB)</th>
                        <th class="py-3 px-4">Aktor Pengguna</th>
                        <th class="py-3 px-4">Faskes Terkait</th>
                        <th class="py-3 px-4">Aksi / Kategori</th>
                        <th class="py-3 px-4">Deskripsi Perubahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DBE5F5]">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono text-[#52627F] shrink-0">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-[#0B1F4B]">{{ $log->actor->name ?? 'Sistem Otomatis' }}</span>
                                <span class="block text-[11px] text-[#52627F]">{{ $log->actor->job_title ?? 'Engine' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-[#0B1F4B]">{{ $log->facility->name ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-[#0B1F4B] border border-slate-300">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-[#0B1F4B]">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-[#52627F]">
                                Belum ada rekaman log audit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auditLogs->hasPages())
            <div class="p-4 border-t border-[#DBE5F5]">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
