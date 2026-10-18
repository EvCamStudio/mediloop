@extends('layouts.mediloop', ['title' => 'Daftar Redistribusi Obat — MediLoop'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#DBE5F5] gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B1F4B]">Daftar Redistribusi & Hibah Antarfaskes</h1>
            <p class="text-xs text-[#52627F] mt-1">Lacak status alur pengajuan dari persetujuan hingga mutasi stok fisik.</p>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Usulkan dari Data Stok</span>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-[#DBE5F5] pb-2 text-xs">
        <a href="{{ route('transfers.index') }}" class="px-3 py-1.5 rounded-md font-semibold transition {{ !request('status') ? 'bg-[#1D4ED8] text-white' : 'text-[#52627F] hover:bg-slate-100' }}">
            Semua Transfer
        </a>
        <a href="{{ route('transfers.index', ['status' => 'PROPOSED']) }}" class="px-3 py-1.5 rounded-md font-semibold transition {{ request('status') === 'PROPOSED' ? 'bg-[#1D4ED8] text-white' : 'text-[#52627F] hover:bg-slate-100' }}">
            Menunggu Otorisasi Donor
        </a>
        <a href="{{ route('transfers.index', ['status' => 'APPROVED']) }}" class="px-3 py-1.5 rounded-md font-semibold transition {{ request('status') === 'APPROVED' ? 'bg-[#1D4ED8] text-white' : 'text-[#52627F] hover:bg-slate-100' }}">
            Menunggu Kesediaan Penerima
        </a>
        <a href="{{ route('transfers.index', ['status' => 'IN_TRANSIT']) }}" class="px-3 py-1.5 rounded-md font-semibold transition {{ request('status') === 'IN_TRANSIT' ? 'bg-[#1D4ED8] text-white' : 'text-[#52627F] hover:bg-slate-100' }}">
            Dalam Pengiriman
        </a>
        <a href="{{ route('transfers.index', ['status' => 'COMPLETED']) }}" class="px-3 py-1.5 rounded-md font-semibold transition {{ request('status') === 'COMPLETED' ? 'bg-[#1D4ED8] text-white' : 'text-[#52627F] hover:bg-slate-100' }}">
            Selesai (Stok Termutasi)
        </a>
    </div>

    <!-- Transfers Table -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-[#DBE5F5] text-[#52627F] uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 font-mono">Kode Transfer</th>
                        <th class="py-3.5 px-4">Obat & Jumlah</th>
                        <th class="py-3.5 px-4">Alur Faskes (Donor &rarr; Penerima)</th>
                        <th class="py-3.5 px-4 text-center font-mono">Match Score</th>
                        <th class="py-3.5 px-4">Status Alur</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DBE5F5]">
                    @forelse($transfers as $t)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Code & Date -->
                            <td class="py-3.5 px-4 font-mono">
                                <span class="font-bold text-[#0B1F4B]">{{ $t->transfer_code }}</span>
                                <span class="block text-[10px] text-[#52627F]">{{ $t->created_at->format('d M Y, H:i') }}</span>
                            </td>

                            <!-- Medicine & Qty -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-[#0B1F4B]">{{ $t->batch->medicine->name }}</span>
                                <span class="block text-[11px] text-[#52627F]">
                                    Jumlah: <strong class="text-[#0B1F4B]">{{ number_format($t->quantity) }}</strong> {{ $t->batch->medicine->unit }} (Batch: {{ $t->batch->batch_number }})
                                </span>
                            </td>

                            <!-- Donor -> Recipient -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="font-semibold text-[#0B1F4B]">{{ $t->donorFacility->name }}</span>
                                    <span class="text-[#52627F]">&rarr;</span>
                                    <span class="font-semibold text-[#1D4ED8]">{{ $t->recipientFacility->name }}</span>
                                </div>
                                <span class="text-[10px] text-[#52627F] block mt-0.5">Jarak Haversine: {{ $t->distance_km }} km</span>
                            </td>

                            <!-- Match Score -->
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-[#1D4ED8]">
                                {{ $t->match_score }}%
                            </td>

                            <!-- Status Badge with Explicit Text -->
                            <td class="py-3.5 px-4">
                                @if($t->status === 'PROPOSED')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-[#B7791F] border border-amber-300">
                                        [MENUNGGU OTORISASI DONOR]
                                    </span>
                                @elseif($t->status === 'APPROVED')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-[#1D4ED8] border border-blue-300">
                                        [MENUNGGU KESEDIAAN PENERIMA]
                                    </span>
                                @elseif($t->status === 'ACCEPTED')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-[#1D4ED8] border border-blue-300">
                                        [SIAP KIRIM]
                                    </span>
                                @elseif($t->status === 'IN_TRANSIT')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-300">
                                        [DALAM PERJALANAN / KURIR]
                                    </span>
                                @elseif($t->status === 'COMPLETED')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-[#0B1F4B] border border-slate-300">
                                        [SELESAI: STOK TERMUTASI]
                                    </span>
                                @elseif($t->status === 'CANCELLED')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-[#D9480F] border border-red-300">
                                        [DIBATALKAN]
                                    </span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('transfers.show', $t) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded text-[#1D4ED8] bg-[#EAF1FF] hover:bg-blue-100 border border-blue-200 transition">
                                    <span>Lacak & Periksa</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#52627F]">
                                Belum ada riwayat pengajuan transfer pada kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div class="p-4 border-t border-[#DBE5F5]">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
