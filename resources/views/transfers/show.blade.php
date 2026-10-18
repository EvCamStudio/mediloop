@extends('layouts.mediloop', ['title' => 'Lacak Alur Transfer #' . $transfer->transfer_code . ' — MediLoop'])

@section('content')
<div class="space-y-6">

    <!-- Header Navigation & Code -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#DBE5F5] gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#52627F] mb-1">
                <a href="{{ route('transfers.index') }}" class="hover:underline text-[#1D4ED8]">&larr; Kembali ke Daftar</a>
                <span>/</span>
                <span class="font-mono">{{ $transfer->transfer_code }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B1F4B]">Alur Redistribusi Hibah Antarfaskes</h1>
        </div>

        <div>
            @if($transfer->status === 'PROPOSED')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-[#B7791F] border border-amber-300">
                    [MENUNGGU OTORISASI DONOR]
                </span>
            @elseif($transfer->status === 'APPROVED')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-[#1D4ED8] border border-blue-300">
                    [MENUNGGU KESEDIAAN PENERIMA]
                </span>
            @elseif($transfer->status === 'ACCEPTED')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-[#1D4ED8] border border-blue-300">
                    [SIAP KIRIM]
                </span>
            @elseif($transfer->status === 'IN_TRANSIT')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-100 text-purple-800 border border-purple-300">
                    [DALAM PERJALANAN / KURIR]
                </span>
            @elseif($transfer->status === 'COMPLETED')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-[#0B1F4B] border border-slate-300">
                    [SELESAI: STOK TERMUTASI]
                </span>
            @elseif($transfer->status === 'CANCELLED')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-red-100 text-[#D9480F] border border-red-300">
                    [DIBATALKAN]
                </span>
            @endif
        </div>
    </div>

    <!-- 5-Stage Visual Progress Tracker -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] p-6 shadow-xs">
        <h3 class="text-xs font-bold uppercase tracking-wider text-[#52627F] mb-6">Tahapan Alur State Machine:</h3>
        
        @php
            $stages = [
                'PROPOSED' => ['num' => 1, 'label' => '1. Pengusulan', 'time' => $transfer->proposed_at],
                'APPROVED' => ['num' => 2, 'label' => '2. Otorisasi Donor', 'time' => $transfer->approved_at],
                'ACCEPTED' => ['num' => 3, 'label' => '3. Kesediaan Penerima', 'time' => $transfer->accepted_at],
                'IN_TRANSIT' => ['num' => 4, 'label' => '4. Pengiriman', 'time' => $transfer->shipped_at],
                'COMPLETED' => ['num' => 5, 'label' => '5. Penerimaan & Mutasi', 'time' => $transfer->completed_at],
            ];
            $statusOrder = ['PROPOSED' => 1, 'APPROVED' => 2, 'ACCEPTED' => 3, 'IN_TRANSIT' => 4, 'COMPLETED' => 5, 'CANCELLED' => 0];
            $currentRank = $statusOrder[$transfer->status] ?? 0;
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            @foreach($stages as $statusKey => $stage)
                @php
                    $isDone = $currentRank >= $stage['num'];
                    $isCurrent = $transfer->status === $statusKey;
                @endphp
                <div class="p-3 rounded-lg border {{ $isCurrent ? 'bg-blue-50 border-[#1D4ED8] ring-1 ring-[#1D4ED8]' : ($isDone ? 'bg-slate-50 border-slate-300' : 'bg-slate-50/40 border-slate-200 opacity-60') }}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold {{ $isCurrent ? 'text-[#1D4ED8]' : ($isDone ? 'text-[#0B1F4B]' : 'text-slate-400') }}">
                            {{ $stage['label'] }}
                        </span>
                        @if($isDone)
                            <svg class="w-4 h-4 text-[#1D4ED8]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                    <span class="text-[10px] text-[#52627F] font-mono block">
                        {{ $stage['time'] ? $stage['time']->format('d M, H:i') : 'Menunggu' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- The Non-Destructive Ledger Invariant Banner -->
    @if($transfer->status !== 'COMPLETED')
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-[#B7791F] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="text-xs text-[#0B1F4B] leading-relaxed">
                <strong class="font-bold text-[#B7791F]">INVARIAN SISTEM AKTIF:</strong>
                Stok fisik pada fasilitas donor ({{ $transfer->donorFacility->name }}) <strong>belum dipotong</strong> dan masih tercatat utuh. Mutasi saldo persediaan donor dan penerima HANYA akan dieksekusi secara atomik saat faskes penerima melakukan <em>Konfirmasi Penerimaan Fisik</em>.
            </div>
        </div>
    @else
        <div class="p-4 bg-slate-100 border border-slate-300 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-[#0B1F4B] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-xs text-[#0B1F4B] leading-relaxed">
                <strong class="font-bold text-[#0B1F4B]">INVARIAN BERHASIL DIEKSEKUSI:</strong>
                Stok fisik sejumlah <strong>{{ $transfer->quantity }} {{ $transfer->batch->medicine->unit }}</strong> telah secara resmi dipotong dari {{ $transfer->donorFacility->name }} dan ditambahkan ke inventaris aktif {{ $transfer->recipientFacility->name }} pada {{ $transfer->completed_at->format('d M Y, H:i') }}.
            </div>
        </div>
    @endif

    <!-- Context-Aware Dynamic Action Controls -->
    @if($transfer->status !== 'COMPLETED' && $transfer->status !== 'CANCELLED')
        <div class="bg-white rounded-xl border-2 border-blue-200 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-[#DBE5F5] mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#0B1F4B]">Tindakan Otorisasi Tahap Ini</h3>
                    <p class="text-xs text-[#52627F]">
                        Status pengguna aktif: <strong class="text-[#0B1F4B]">{{ $user->name }}</strong> ({{ $user->job_title }})
                    </p>
                </div>
                <span class="text-xs font-mono text-[#1D4ED8]">Peran: {{ $user->role }}</span>
            </div>

            <!-- Action 1: Donor Approver (Head of Pharmacy) Approves Proposed Transfer -->
            @if($transfer->status === 'PROPOSED')
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-xs text-[#52627F]">
                        Menunggu verifikasi dan tanda tangan persetujuan resmi dari Kepala Instalasi Farmasi faskes donor.
                    </p>
                    @if($user->canApproveDonorTransfer() || $user->isDinkesAuditor())
                        <form method="POST" action="{{ route('transfers.approve', $transfer) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                                Setujui Pengeluaran Obat Resmi (Approve)
                            </button>
                        </form>
                    @else
                        <span class="px-3 py-1.5 text-xs font-medium rounded bg-slate-100 text-slate-500 italic">
                            Hanya Kepala Farmasi Donor yang berwenang menyetujui. (Gunakan switcher demo di kanan bawah)
                        </span>
                    @endif
                </div>
            @endif

            <!-- Action 2: Recipient Accepts Transfer -->
            @if($transfer->status === 'APPROVED')
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-xs text-[#52627F]">
                        Donor telah menyetujui. Menunggu konfirmasi kesediaan menerima alokasi dari pihak faskes penerima.
                    </p>
                    @if($user->canAcceptRecipientTransfer() || $user->isDinkesAuditor())
                        <form method="POST" action="{{ route('transfers.accept', $transfer) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                                Konfirmasi Kesediaan Menerima Alokasi (Accept)
                            </button>
                        </form>
                    @else
                        <span class="px-3 py-1.5 text-xs font-medium rounded bg-slate-100 text-slate-500 italic">
                            Hanya Apoteker/Kepala Penerima yang berwenang. (Gunakan switcher demo di kanan bawah)
                        </span>
                    @endif
                </div>
            @endif

            <!-- Action 3: Dispatch Shipment (Input Courier & Tracking) -->
            @if($transfer->status === 'ACCEPTED')
                <form method="POST" action="{{ route('transfers.dispatch', $transfer) }}" class="space-y-4">
                    @csrf
                    <p class="text-xs text-[#52627F]">
                        Faskes penerima siap menerima. Masukkan rincian kurir pengiriman berizin untuk memberangkatkan obat:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="courier_type" class="block text-xs font-bold text-[#0B1F4B] mb-1">Jenis Kurir / Transportasi:</label>
                            <input type="text" id="courier_type" name="courier_type" value="Kurir Khusus Farmasi Berpendingin (Cold-Box)" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs" required>
                        </div>
                        <div>
                            <label for="tracking_number" class="block text-xs font-bold text-[#0B1F4B] mb-1">Nomor Resi / Surat Jalan Ekspedisi:</label>
                            <input type="text" id="tracking_number" name="tracking_number" value="EXP-DKI-{{ rand(1000, 9999) }}" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono" required>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-purple-700 hover:bg-purple-800 rounded-lg shadow-xs transition">
                            Berangkatkan Pengiriman Obat (Dispatch)
                        </button>
                    </div>
                </form>
            @endif

            <!-- Action 4: Confirm Physical Arrival & Complete (Atomic Stock Mutation) -->
            @if($transfer->status === 'IN_TRANSIT')
                <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-purple-900">Obat Sedang Dalam Perjalanan Kurir:</span>
                        <span class="font-mono text-purple-800">Resi: {{ $transfer->tracking_number }} ({{ $transfer->courier_type }})</span>
                    </div>
                    <p class="text-xs text-[#52627F]">
                        Fisik obat telah tiba di fasilitas penerima? Kepala Faskes Penerima wajib memeriksa kondisi fisik, segel kemasan, dan suhu dingin sebelum konfirmasi.
                    </p>
                    <div class="flex justify-end pt-2">
                        @if($user->canConfirmPhysicalArrival() || $user->isDinkesAuditor())
                            <form method="POST" action="{{ route('transfers.complete', $transfer) }}">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#0B1F4B] hover:bg-blue-900 rounded-lg shadow-xs transition">
                                    Konfirmasi Penerimaan Fisik & Mutasi Stok Otomatis
                                </button>
                            </form>
                        @else
                            <span class="px-3 py-1.5 text-xs font-medium rounded bg-slate-100 text-slate-500 italic">
                                Hanya Kepala Faskes Penerima yang berwenang menyelesaikan penerimaan fisik.
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Transfer Logistics & Clinical Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Medicine & Facilities -->
        <div class="bg-white rounded-xl border border-[#DBE5F5] p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#0B1F4B] pb-3 border-b border-[#DBE5F5]">Informasi Obat & Logistik</h3>
            
            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-[#52627F]">Nama Obat:</span>
                    <span class="font-bold text-[#0B1F4B]">{{ $transfer->batch->medicine->name }}</span>
                </div>
                <div class="flex justify-between font-mono">
                    <span class="text-[#52627F]">Nomor Batch:</span>
                    <span class="font-bold text-[#0B1F4B]">{{ $transfer->batch->batch_number }}</span>
                </div>
                <div class="flex justify-between font-mono">
                    <span class="text-[#52627F]">Jumlah Dihibahkan:</span>
                    <span class="font-bold text-[#1D4ED8]">{{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#52627F]">Faskes Donor:</span>
                    <span class="font-semibold text-[#0B1F4B]">{{ $transfer->donorFacility->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#52627F]">Faskes Penerima:</span>
                    <span class="font-semibold text-[#1D4ED8]">{{ $transfer->recipientFacility->name }}</span>
                </div>
                <div class="flex justify-between font-mono">
                    <span class="text-[#52627F]">Jarak Tempuh:</span>
                    <span class="font-bold text-[#0B1F4B]">{{ $transfer->distance_km }} km</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Scoring Weights & Safeguard Cap -->
        <div class="bg-white rounded-xl border border-[#DBE5F5] p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#0B1F4B] pb-3 border-b border-[#DBE5F5]">Hasil Penilaian Matching & Safeguard Cap</h3>
            
            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between font-mono">
                    <span class="text-[#52627F]">Skor Kebutuhan Riil (50%):</span>
                    <span class="font-bold text-[#0B1F4B]">{{ $transfer->need_score }}%</span>
                </div>
                <div class="flex justify-between font-mono">
                    <span class="text-[#52627F]">Skor Jarak Haversine (30%):</span>
                    <span class="font-bold text-[#0B1F4B]">{{ $transfer->proximity_score }}%</span>
                </div>
                <div class="flex justify-between font-mono">
                    <span class="text-[#52627F]">Skor Kesiapan Logistik (20%):</span>
                    <span class="font-bold text-[#0B1F4B]">{{ $transfer->logistics_score }}%</span>
                </div>
                <div class="flex justify-between font-mono pt-2 border-t border-[#DBE5F5]">
                    <span class="font-bold text-[#0B1F4B]">Skor Kecocokan Komposit:</span>
                    <span class="font-extrabold text-sm text-[#1D4ED8]">{{ $transfer->match_score }}%</span>
                </div>
                <div class="flex justify-between font-mono pt-2 border-t border-[#DBE5F5]">
                    <span class="text-[#52627F]">Batas Serapan Penerima (Cap):</span>
                    <span class="font-bold text-[#0B1F4B]">{{ $transfer->recipient_intake_cap }} unit</span>
                </div>
                <p class="text-[10px] text-[#52627F] leading-tight">
                    *Batas serapan menjamin bahwa faskes penerima memiliki kecepatan pakai cukup untuk menghabiskan obat sebelum batas kedaluwarsa.
                </p>
            </div>
        </div>
    </div>

    <!-- Immutable Audit Trail History for this Transfer -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] shadow-xs overflow-hidden">
        <div class="p-4 border-b border-[#DBE5F5] bg-slate-50">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#0B1F4B]">Log Audit & Bukti Riwayat Otorisasi</h3>
        </div>
        <div class="divide-y divide-[#DBE5F5] text-xs">
            @forelse($transfer->auditLogs as $log)
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="font-semibold text-[#0B1F4B]">{{ $log->description }}</div>
                        <div class="text-[11px] text-[#52627F] mt-0.5">
                            Aktor: <strong class="text-[#0B1F4B]">{{ $log->actor->name ?? 'Sistem' }}</strong> ({{ $log->actor->job_title ?? 'Automated Engine' }})
                        </div>
                    </div>
                    <span class="text-[11px] font-mono text-[#52627F] shrink-0">
                        {{ $log->created_at->format('d M Y, H:i:s') }}
                    </span>
                </div>
            @empty
                <div class="p-4 text-center text-[#52627F]">
                    Belum ada catatan log audit tambahan.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
