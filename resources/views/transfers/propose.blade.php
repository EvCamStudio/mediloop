@extends('layouts.mediloop', ['title' => 'Rekomendasi & Pengusulan Transfer — MediLoop'])

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb & Navigation -->
    <div class="flex items-center gap-2 text-xs text-[#52627F]">
        <a href="{{ route('dashboard') }}" class="hover:underline text-[#1D4ED8]">Dashboard</a>
        <span>/</span>
        <a href="{{ route('transfers.index') }}" class="hover:underline text-[#1D4ED8]">Redistribusi</a>
        <span>/</span>
        <span class="text-[#0B1F4B] font-semibold">Usulan Transfer Batch #{{ $batch->batch_number }}</span>
    </div>

    <!-- Mandatory Regulatory Disclaimer Banner (PRD Rule) -->
    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-start gap-3">
        <svg class="w-5 h-5 text-[#1D4ED8] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="text-xs text-[#0B1F4B] leading-relaxed">
            <strong class="font-bold">PEMBERITAHUAN KEPATUHAN REGULASI KEFARMASIAN (PERMENKES RI):</strong>
            Platform MediLoop bertindak murni sebagai <em>clinical decision-support system</em>. Sistem ini bukan pasar bebas atau e-commerce komersial. Seluruh pengalihan obat hibah/redistribusi antarfaskes wajib mendapatkan persetujuan tertulis dari Kepala Instalasi Farmasi faskes donor dan verifikasi penerimaan fisik oleh Kepala Faskes penerima.
        </div>
    </div>

    <!-- Source Batch Card -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#DBE5F5] gap-2">
            <div>
                <span class="text-xs font-mono text-[#52627F]">SUMBER OBAT DONOR</span>
                <h2 class="text-xl font-extrabold text-[#0B1F4B]">{{ $batch->medicine->name }}</h2>
                <span class="text-xs text-[#52627F]">{{ $batch->facility->name }} • Batch: <strong class="font-mono text-[#0B1F4B]">{{ $batch->batch_number }}</strong></span>
            </div>
            <div>
                <span class="px-3 py-1 text-xs font-bold rounded bg-orange-100 text-[#D9480F] border border-orange-300">
                    RISIKO TINGGI: {{ $batch->projected_waste }} UNIT LIMBAH TERPROYEKSI
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-xs font-mono">
            <div class="p-3 bg-slate-50 rounded-lg">
                <span class="text-[#52627F] block">Stok Aktif Donor:</span>
                <span class="text-base font-bold text-[#0B1F4B]">{{ number_format($batch->current_stock) }} {{ $batch->medicine->unit }}</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <span class="text-[#52627F] block">Burn Rate Donor:</span>
                <span class="text-base font-bold text-[#0B1F4B]">{{ $batch->daily_burn_rate }} unit/hari</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <span class="text-[#52627F] block">Sisa Waktu Expiry:</span>
                <span class="text-base font-bold text-[#D9480F]">{{ $batch->days_to_expiry }} hari</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <span class="text-[#52627F] block">Proyeksi Terbuang:</span>
                <span class="text-base font-bold text-[#D9480F]">{{ $batch->projected_waste }} unit ({{ round($batch->waste_ratio * 100, 1) }}%)</span>
            </div>
        </div>
    </div>

    <!-- Candidate Matching Algorithm Results (50% Need + 30% Proximity + 20% Logistics) -->
    <div class="bg-white rounded-xl border border-[#DBE5F5] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#DBE5F5] flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-[#0B1F4B]">Rekomendasi Kandidat Faskes Penerima (Scoring Model)</h3>
                <p class="text-xs text-[#52627F]">
                    Peringkat dihitung berdasarkan: <strong class="text-[#0B1F4B]">50% Kebutuhan Riil + 30% Jarak Haversine + 20% Logistik/Cold-Chain</strong>.
                </p>
            </div>
            <span class="px-2.5 py-1 text-xs font-medium text-[#1D4ED8] bg-[#EAF1FF] rounded border border-blue-200">
                {{ count($candidates) }} Faskes Teranalisis
            </span>
        </div>

        <!-- Sort toggle -->
        <div class="px-5 py-3 border-b border-[#DBE5F5] flex flex-wrap items-center gap-2 bg-white">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#52627F]">Urutkan</span>
            <a href="{{ route('transfers.propose', ['batch' => $batch, 'sort' => 'score']) }}"
                aria-current="{{ $sort === 'score' ? 'true' : 'false' }}"
                class="px-3 py-1.5 text-xs font-semibold rounded border transition {{ $sort === 'score' ? 'bg-[#1D4ED8] text-white border-[#1D4ED8]' : 'bg-white text-[#52627F] border-[#DBE5F5] hover:bg-[#EAF1FF]' }}">
                Skor Terbaik
            </a>
            <a href="{{ route('transfers.propose', ['batch' => $batch, 'sort' => 'distance']) }}"
                aria-current="{{ $sort === 'distance' ? 'true' : 'false' }}"
                class="px-3 py-1.5 text-xs font-semibold rounded border transition {{ $sort === 'distance' ? 'bg-[#1D4ED8] text-white border-[#1D4ED8]' : 'bg-white text-[#52627F] border-[#DBE5F5] hover:bg-[#EAF1FF]' }}">
                Terdekat
            </a>
            <span class="ml-auto text-[11px] text-[#52627F]">Jarak udara (Haversine), bukan jarak tempuh jalan.</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-[#DBE5F5] text-[#52627F] uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">Peringkat & Faskes Penerima</th>
                        <th class="py-3 px-4 text-right font-mono">Jarak (Haversine)</th>
                        <th class="py-3 px-4 text-center">Bobot Kebutuhan (50%)</th>
                        <th class="py-3 px-4 text-center">Bobot Jarak (30%)</th>
                        <th class="py-3 px-4 text-center">Bobot Logistik (20%)</th>
                        <th class="py-3 px-4 text-right font-mono">Match Score</th>
                        <th class="py-3 px-4 text-right font-mono">Batas Serapan (Cap)</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DBE5F5]">
                    @foreach($candidates as $index => $c)
                        <tr class="hover:bg-blue-50/40 transition {{ $index === 0 ? 'bg-blue-50/20' : '' }}">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center font-bold text-[10px] {{ $index === 0 ? 'bg-[#1D4ED8] text-white' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-[#0B1F4B]">{{ $c['facility']->name }}</div>
                                        <div class="text-[11px] text-[#52627F]">{{ $c['facility']->type }} • {{ $c['facility']->address }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3 px-4 text-right font-mono font-semibold text-[#0B1F4B]">
                                {{ $c['distance_km'] }} km
                            </td>

                            <td class="py-3 px-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $c['need_score'] >= 80 ? 'bg-orange-100 text-[#D9480F]' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $c['need_score'] }}%
                                </span>
                            </td>

                            <td class="py-3 px-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-[#1D4ED8]">
                                    {{ $c['proximity_score'] }}%
                                </span>
                            </td>

                            <td class="py-3 px-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-[#0B1F4B]">
                                    {{ $c['logistics_score'] }}%
                                </span>
                            </td>

                            <td class="py-3 px-4 text-right font-mono font-extrabold text-sm text-[#1D4ED8]">
                                {{ $c['match_score'] }}%
                            </td>

                            <td class="py-3 px-4 text-right font-mono">
                                <span class="font-bold text-[#0B1F4B]">{{ $c['recipient_intake_cap'] }} unit</span>
                                <span class="block text-[10px] text-[#52627F]">Bakar: {{ $c['recipient_daily_burn_rate'] }}/hari</span>
                            </td>

                            <td class="py-3 px-4 text-right">
                                <button type="button" 
                                    onclick="selectCandidate({{ $c['facility']->id }}, '{{ addslashes($c['facility']->name) }}', {{ $c['recommended_quantity'] }})"
                                    class="px-3 py-1.5 text-xs font-semibold rounded text-white bg-[#1D4ED8] hover:bg-blue-800 transition">
                                    Pilih Faskes
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Proposal Submission Form -->
    <div id="proposalForm" class="bg-white rounded-xl border border-[#DBE5F5] p-6 shadow-xs">
        <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Formulir Pengusulan Transfer Hibah</h3>
        <p class="text-xs text-[#52627F] mb-6">Usulan ini akan diajukan ke Kepala Instalasi Farmasi untuk mendapatkan otorisasi pengeluaran resmi.</p>

        <form method="POST" action="{{ route('transfers.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="batch_id" value="{{ $batch->id }}">
            <input type="hidden" id="selectedFacilityId" name="recipient_facility_id" value="{{ $candidates[0]['facility']->id ?? '' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Selected Recipient Facility -->
                <div>
                    <label class="block text-xs font-bold text-[#0B1F4B] mb-1">Fasilitas Penerima Terpilih:</label>
                    <input type="text" id="selectedFacilityName" readonly value="{{ $candidates[0]['facility']->name ?? '' }}" class="w-full px-3 py-2 bg-slate-50 border border-[#DBE5F5] rounded-lg text-xs font-semibold text-[#0B1F4B]">
                </div>

                <!-- Transfer Quantity -->
                <div>
                    <label for="quantity" class="block text-xs font-bold text-[#0B1F4B] mb-1">Jumlah Unit yang Diredistribusikan:</label>
                    <div class="relative">
                        <input type="number" id="quantity" name="quantity" min="1" max="{{ $batch->current_stock }}" value="{{ $candidates[0]['recommended_quantity'] ?? 100 }}" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs font-mono font-bold focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none" required>
                        <span class="absolute right-3 top-2 text-xs text-[#52627F]">{{ $batch->medicine->unit }}</span>
                    </div>
                    <span class="text-[11px] text-[#52627F] block mt-1">Maksimal stok tersedia di donor: {{ $batch->current_stock }} unit.</span>
                </div>
            </div>

            <!-- Notes & Justification -->
            <div>
                <label for="notes" class="block text-xs font-bold text-[#0B1F4B] mb-1">Catatan Justifikasi Klinis & Logistik:</label>
                <textarea id="notes" name="notes" rows="2" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-xs focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none" placeholder="Contoh: Redistribusi untuk mengatasi lonjakan kasus infeksi dan mencegah kedaluwarsa sekunder.">Pengusulan redistribusi surplus stok Ceftriaxone untuk mencegah pemusnahan obat kedaluwarsa dan mendukung ketersediaan obat di puskesmas sekitar.</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-[#DBE5F5] flex items-center justify-between">
                <span class="text-xs text-[#52627F]">
                    Pengusul: <strong class="text-[#0B1F4B]">{{ $user->name }}</strong> ({{ $user->job_title }})
                </span>

                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition inline-flex items-center gap-1.5">
                    <span>Ajukan Permohonan Transfer</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function selectCandidate(facilityId, facilityName, recommendedQty) {
        document.getElementById('selectedFacilityId').value = facilityId;
        document.getElementById('selectedFacilityName').value = facilityName;
        document.getElementById('quantity').value = recommendedQty;
        document.getElementById('proposalForm').scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endsection
