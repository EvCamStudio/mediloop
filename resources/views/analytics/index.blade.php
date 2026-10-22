@extends('layouts.mediloop', ['title' => 'Analitik & Statistik Prediktif — MediLoop'])

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#DBE5F5] gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#0B1F4B]">Analitik &amp; Visualisasi Prediktif</h1>
                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-blue-100 text-[#1D4ED8]">
                    {{ $facility?->name ?? 'Fasilitas Kesehatan Terhubung' }}
                </span>
            </div>
            <p class="text-xs text-[#52627F] mt-1">
                Kalkulasi analitik data pemakaian (*burn rate*), estimasi limbah kedaluwarsa, dan efisiensi redistribusi obat.
            </p>
        </div>

        @if(($user->isDinkesAuditor() || $user->role === 'SYSADMIN') && isset($facilitiesList))
            <form method="GET" action="{{ route('analytics.index') }}" class="flex items-center gap-2">
                <label for="facility_id" class="text-xs font-bold text-[#52627F] whitespace-nowrap">Filter Faskes:</label>
                <select name="facility_id" id="facility_id" onchange="this.form.submit()" class="px-3 py-2 bg-white border border-[#DBE5F5] rounded-lg text-xs font-semibold text-[#0B1F4B] focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none shadow-2xs">
                    <option value="">Semua Fasilitas (Wilayah)</option>
                    @foreach($facilitiesList as $f)
                        <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <!-- 4 KPI SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Stock Value -->
        <div class="bg-white p-5 rounded-xl border border-[#DBE5F5] shadow-xs">
            <div class="text-[11px] font-bold text-[#52627F] uppercase tracking-wider">Total Nilai Inventaris</div>
            <div class="text-2xl font-black text-[#0B1F4B] mt-1">
                Rp {{ number_format($totalInventoryValueIdr, 0, ',', '.') }}
            </div>
            <div class="text-[11.5px] text-[#52627F] mt-1 flex items-center justify-between">
                <span>{{ number_format($totalStockUnits) }} unit fisik</span>
                <span class="font-bold text-blue-700">{{ $totalBatches }} batch</span>
            </div>
        </div>

        <!-- Projected Waste Value -->
        <div class="bg-white p-5 rounded-xl border border-red-200 shadow-xs relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-red-500"></div>
            <div class="text-[11px] font-bold text-red-600 uppercase tracking-wider">Potensi Kerugian Limbah</div>
            <div class="text-2xl font-black text-red-600 mt-1">
                Rp {{ number_format($totalWasteValueIdr, 0, ',', '.') }}
            </div>
            <div class="text-[11.5px] text-[#52627F] mt-1 flex items-center justify-between">
                <span>{{ number_format($totalWasteUnits) }} unit terancam</span>
                <span class="font-bold text-red-700">{{ $wasteRatio }}% dari stok</span>
            </div>
        </div>

        <!-- Stock Efficiency Score -->
        <div class="bg-white p-5 rounded-xl border border-[#DBE5F5] shadow-xs">
            <div class="text-[11px] font-bold text-[#52627F] uppercase tracking-wider">Skor Efisiensi Inventaris</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">
                {{ $efficiencyScore }}%
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-2">
                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, max(0, $efficiencyScore)) }}%"></div>
            </div>
            <div class="text-[11px] text-[#52627F] mt-1 text-right font-medium">Berdasarkan laju pemakaian obat</div>
        </div>

        <!-- Saved via Redistribution -->
        <div class="bg-white p-5 rounded-xl border border-blue-200 shadow-xs relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-blue-600"></div>
            <div class="text-[11px] font-bold text-[#1D4ED8] uppercase tracking-wider">Penyelamatan Redistribusi</div>
            <div class="text-2xl font-black text-[#1D4ED8] mt-1">
                Rp {{ number_format($savedValueIdr, 0, ',', '.') }}
            </div>
            <div class="text-[11.5px] text-[#52627F] mt-1 flex items-center justify-between">
                <span>{{ number_format($savedUnits) }} unit diselamatkan</span>
                <span class="font-bold text-emerald-700">Terselesaikan</span>
            </div>
        </div>
    </div>

    <!-- CHARTS ROW 1: Donut (Risk Breakdown) + Bar (Waste Value by Category) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Donut Chart: Risk Distribution -->
        <div class="lg:col-span-5 bg-white p-5 rounded-xl border border-[#DBE5F5] shadow-xs flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-[#DBE5F5]">
                <div>
                    <h2 class="text-sm font-bold text-[#0B1F4B]">Distribusi Tingkat Risiko Obat</h2>
                    <p class="text-[11px] text-[#52627F]">Proporsi batch berdasarkan rasio hari kedaluwarsa &amp; laju konsumsi</p>
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $totalBatches }} Batch</span>
            </div>
            
            <div class="py-4 flex-1 flex items-center justify-center relative min-h-[240px]">
                <canvas id="riskDonutChart" class="max-h-[220px]"></canvas>
            </div>

            <!-- Legend Pills -->
            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-600 shrink-0"></span>
                    <span class="text-slate-600">Kritis: <b class="text-[#0B1F4B]">{{ $riskCounts['CRITICAL'] ?? 0 }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-slate-600">Tinggi: <b class="text-[#0B1F4B]">{{ $riskCounts['HIGH'] ?? 0 }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 shrink-0"></span>
                    <span class="text-slate-600">Sedang: <b class="text-[#0B1F4B]">{{ $riskCounts['MEDIUM'] ?? 0 }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="text-slate-600">Aman: <b class="text-[#0B1F4B]">{{ $riskCounts['LOW'] ?? 0 }}</b></span>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Category Waste Value -->
        <div class="lg:col-span-7 bg-white p-5 rounded-xl border border-[#DBE5F5] shadow-xs flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-[#DBE5F5]">
                <div>
                    <h2 class="text-sm font-bold text-[#0B1F4B]">Potensi Nilai Limbah per Golongan Obat (Rp)</h2>
                    <p class="text-[11px] text-[#52627F]">Estimasi kerugian finansial jika surplus tidak diredistribusi ke faskes lain</p>
                </div>
                <span class="px-2 py-0.5 text-[10.5px] font-bold rounded bg-slate-100 text-slate-700">Rupiah (IDR)</span>
            </div>

            <div class="py-4 flex-1 flex items-center justify-center relative min-h-[250px]">
                <canvas id="categoryWasteChart"></canvas>
            </div>
        </div>
    </div>

    <!-- CHARTS ROW 2: Expiry Horizon Buckets + Top Redundant Batches Table -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Expiry Horizon Timeline (Bar Chart) -->
        <div class="lg:col-span-5 bg-white p-5 rounded-xl border border-[#DBE5F5] shadow-xs flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-[#DBE5F5]">
                <div>
                    <h2 class="text-sm font-bold text-[#0B1F4B]">Horizon Waktu Kedaluwarsa</h2>
                    <p class="text-[11px] text-[#52627F]">Banyaknya batch yang mendekati tanggal kedaluwarsa</p>
                </div>
            </div>

            <div class="py-4 flex-1 flex items-center justify-center relative min-h-[220px]">
                <canvas id="expiryHorizonChart"></canvas>
            </div>
        </div>

        <!-- Top Redundancy Index Table (Slowest Burn Rate vs Expiry) -->
        <div class="lg:col-span-7 bg-white p-5 rounded-xl border border-[#DBE5F5] shadow-xs flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-[#DBE5F5]">
                <div>
                    <h2 class="text-sm font-bold text-[#0B1F4B]">Indeks Surplus Tertinggi (Perlu Segera Dialihkan)</h2>
                    <p class="text-[11px] text-[#52627F]">Obat dengan waktu habis jauh melebihi sisa hari kedaluwarsa (Surplus Ratio &gt; 1)</p>
                </div>
                <a href="{{ route('batches.index') }}" class="text-xs font-bold text-[#1D4ED8] hover:underline">Kelola Stok &rarr;</a>
            </div>

            <div class="overflow-x-auto py-2 flex-1">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-[#DBE5F5] text-[#52627F] font-bold text-[11px]">
                            <th class="py-2.5 px-3">Nama Obat &amp; Batch</th>
                            <th class="py-2.5 px-3">Stok / Laju Harian</th>
                            <th class="py-2.5 px-3">Hari Habis vs Exp</th>
                            <th class="py-2.5 px-3">Surplus Ratio</th>
                            <th class="py-2.5 px-3 text-right">Potensi Limbah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($burnRateAnomalies as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-2.5 px-3">
                                    <div class="font-bold text-[#0B1F4B]">{{ $item['medicine_name'] }}</div>
                                    <div class="text-[11px] text-[#52627F] font-mono">{{ $item['batch_number'] }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-slate-700">
                                    <div><b>{{ number_format($item['current_stock']) }}</b> unit</div>
                                    <div class="text-[11px] text-slate-500">{{ $item['daily_burn_rate'] }} unit/hari</div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="text-[#0B1F4B] font-semibold">{{ $item['days_to_deplete'] }} hari habis</div>
                                    <div class="text-[11px] text-red-600 font-bold">sisa {{ $item['days_to_expiry'] }} hari exp</div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold {{ $item['surplus_ratio'] >= 2 ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                        {{ $item['surplus_ratio'] }}x Surplus
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <div class="font-bold text-red-600">{{ number_format($item['projected_waste']) }} unit</div>
                                    <div class="text-[10.5px] text-slate-500">Rp {{ number_format($item['waste_value'], 0, ',', '.') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Belum ada obat dengan surplus signifikan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- Chart.js Script Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. DONUT CHART: Risk Breakdown
    const riskCtx = document.getElementById('riskDonutChart').getContext('2d');
    new Chart(riskCtx, {
        type: 'doughnut',
        data: {
            labels: ['Kritis (Critical)', 'Tinggi (High)', 'Sedang (Medium)', 'Aman (Low)'],
            datasets: [{
                data: [
                    {{ $riskCounts['CRITICAL'] ?? 0 }},
                    {{ $riskCounts['HIGH'] ?? 0 }},
                    {{ $riskCounts['MEDIUM'] ?? 0 }},
                    {{ $riskCounts['LOW'] ?? 0 }}
                ],
                backgroundColor: ['#DC2626', '#F59E0B', '#FBBF24', '#10B981'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const val = context.raw || 0;
                            const total = {{ max(1, $totalBatches) }};
                            const pct = ((val / total) * 100).toFixed(1);
                            return ` ${context.label}: ${val} batch (${pct}%)`;
                        }
                    }
                }
            },
            cutout: '68%'
        }
    });

    // 2. BAR CHART: Category Waste Value
    @php
        $catLabels = array_keys(array_slice($categoryData, 0, 6, true));
        $catValues = array_map(fn($k) => $categoryData[$k]['projected_waste_value_idr'], $catLabels);
    @endphp

    const catCtx = document.getElementById('categoryWasteChart').getContext('2d');
    new Chart(catCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($catLabels) !!},
            datasets: [{
                label: 'Estimasi Limbah (Rp)',
                data: {!! json_encode($catValues) !!},
                backgroundColor: '#1D4ED8',
                hoverBackgroundColor: '#1E40AF',
                borderRadius: 6,
                maxBarThickness: 38
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const val = context.raw || 0;
                            return ' Potensi Limbah: Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (val) {
                            if (val >= 1000000) return (val / 1000000) + ' Jt';
                            if (val >= 1000) return (val / 1000) + ' Rb';
                            return val;
                        },
                        font: { size: 10 }
                    },
                    grid: { color: '#F1F5F9' }
                },
                x: {
                    ticks: { font: { size: 10.5 }, maxRotation: 20 },
                    grid: { display: false }
                }
            }
        }
    });

    // 3. BAR CHART: Expiry Horizon Buckets
    const horizonCtx = document.getElementById('expiryHorizonChart').getContext('2d');
    new Chart(horizonCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($expiryBuckets)) !!},
            datasets: [{
                label: 'Jumlah Batch',
                data: {!! json_encode(array_values($expiryBuckets)) !!},
                backgroundColor: ['#DC2626', '#EA580C', '#F59E0B', '#10B981'],
                borderRadius: 6,
                maxBarThickness: 32
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ' ' + ctx.raw + ' Batch Obat';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { size: 10 } },
                    grid: { color: '#F1F5F9' }
                },
                x: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
