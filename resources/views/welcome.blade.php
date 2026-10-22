<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MediLoop — Platform Pendukung Keputusan Redistribusi Obat Antarfaskes</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand-primary: #1D4ED8;
            --brand-navy: #0B1F4B;
            --brand-sky: #EAF1FF;
            --brand-border: #DBE5F5;
            --brand-muted: #52627F;
            --brand-risk: #D9480F;
            --brand-warning: #B7791F;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: #F8FAFC;
            color: #0B1F4B;
        }
    </style>
</head>

<body
    class="min-h-full flex flex-col bg-slate-50 text-slate-900 antialiased selection:bg-blue-100 selection:text-blue-900">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-[#DBE5F5] sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-[#1D4ED8] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-[#0B1F4B]">MediLoop</span>
                    <span
                        class="hidden md:inline-block ml-2 px-2 py-0.5 text-[10px] font-semibold text-blue-800 bg-[#EAF1FF] rounded border border-[#DBE5F5]">Decision
                        Support Only</span>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                <a href="#simulator"
                    class="hidden sm:inline-block text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] px-3 py-2">
                    Kalkulator Simulasi
                </a>
                <a href="#prinsip"
                    class="hidden sm:inline-block text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] px-3 py-2">
                    Prinsip Sistem
                </a>
                <a href="{{ route('demo.switch', 'donor-staff') }}"
                    class="px-4 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                    Buka Dashboard
                </a>
            </nav>
        </div>
    </header>

    <!-- Hero Section with 3D Capsule Vector Focal Anchor -->
    <section class="relative bg-white border-b border-[#DBE5F5] py-16 lg:py-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                <!-- Left: Clinical Value Proposition -->
                <div class="lg:col-span-7">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF1FF] border border-[#DBE5F5] text-xs font-semibold text-[#1D4ED8] mb-6">
                        <span class="w-2 h-2 rounded-full bg-[#1D4ED8]"></span>
                        Sistem Pendukung Keputusan Hibah & Redistribusi Obat Antarfaskes
                    </div>

                    <h1
                        class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B1F4B] tracking-tight leading-tight mb-6">
                        Cegah Pemusnahan Obat Kedaluwarsa Melalui Koordinasi Antarfaskes.
                    </h1>

                    <p class="text-base sm:text-lg text-[#52627F] leading-relaxed mb-8">
                        MediLoop mendeteksi surplus obat berisiko kedaluwarsa di rumah sakit dan mencocokkannya dengan
                        kebutuhan riil puskesmas terdekat secara terukur, aman, dan patuh regulasi Kemenkes.
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('demo.switch', 'donor-staff') }}"
                            class="px-5 py-3 text-sm font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition inline-flex items-center gap-2">
                            <span>Mulai Simulasi Demo</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="#simulator"
                            class="px-5 py-3 text-sm font-semibold text-[#0B1F4B] bg-slate-100 hover:bg-slate-200 rounded-lg transition border border-[#DBE5F5]">
                            Uji Algoritma Prediksi Limbah
                        </a>
                    </div>

                    <!-- Hard Operational Constraints (No PHI, Decision-Support Only) -->
                    <div class="grid grid-cols-3 gap-4 mt-10 pt-8 border-t border-[#DBE5F5] text-xs">
                        <div>
                            <span class="block font-bold text-[#0B1F4B]">Zero Data Pasien</span>
                            <span class="text-[#52627F]">Logistik batch tanpa identitas rekam medis (PHI).</span>
                        </div>
                        <div>
                            <span class="block font-bold text-[#0B1F4B]">Persetujuan Manusia</span>
                            <span class="text-[#52627F]">Wajib disahkan Kepala Instalasi Farmasi.</span>
                        </div>
                        <div>
                            <span class="block font-bold text-[#0B1F4B]">Invarian Mutlak</span>
                            <span class="text-[#52627F]">Stok hanya termutasi saat fisik terkonfirmasi tiba.</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Visual Anchor (Pharmaceutical 3D Capsule & Ledger Stamp) -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-md p-6 bg-white rounded-2xl border border-[#DBE5F5] shadow-lg relative">
                        <div class="flex items-center justify-between pb-4 border-b border-[#DBE5F5]">
                            <div>
                                <span class="text-[11px] font-mono text-[#52627F]">KASUS BENCHMARK PRD</span>
                                <h3 class="text-sm font-bold text-[#0B1F4B]">Ceftriaxone 1g Injeksi</h3>
                            </div>
                            <span
                                class="px-2 py-0.5 text-xs font-bold text-[#D9480F] bg-orange-50 border border-orange-200 rounded">
                                [RISIKO TINGGI: 300 UNIT]
                            </span>
                        </div>

                        <!-- Capsule & Ledger Graphic -->
                        <div class="my-6 flex justify-center">
                            <svg class="w-48 h-32" viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="30" y="30" width="140" height="60" rx="30" fill="#EAF1FF" stroke="#1D4ED8"
                                    stroke-width="4" />
                                <path d="M100 30 C133 30 170 30 170 60 C170 90 133 90 100 90 Z" fill="#1D4ED8" />
                                <line x1="100" y1="30" x2="100" y2="90" stroke="#0B1F4B" stroke-width="3"
                                    stroke-dasharray="4 2" />
                                <circle cx="65" cy="60" r="12" fill="#DBE5F5" />
                                <circle cx="135" cy="60" r="12" fill="#93C5FD" />
                                <text x="45" y="108" font-family="monospace" font-size="9" fill="#52627F">BATCH:
                                    CFT-2026-X89</text>
                            </svg>
                        </div>

                        <!-- Data Ledger Spec -->
                        <div
                            class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-lg text-xs font-mono border border-slate-200">
                            <div>
                                <span class="text-[#52627F] block">Stok Aktif:</span>
                                <span class="font-bold text-[#0B1F4B]">480 Vial</span>
                            </div>
                            <div>
                                <span class="text-[#52627F] block">Laju Terpakai:</span>
                                <span class="font-bold text-[#0B1F4B]">4.0 Unit/hari</span>
                            </div>
                            <div>
                                <span class="text-[#52627F] block">Sisa Waktu:</span>
                                <span class="font-bold text-[#0B1F4B]">45 Hari</span>
                            </div>
                            <div>
                                <span class="text-[#52627F] block">Proyeksi Limbah:</span>
                                <span class="font-bold text-[#D9480F]">300 Unit (62.5%)</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#DBE5F5] flex items-center justify-between text-xs">
                            <span class="text-[#52627F]">Rekomendasi Matching:</span>
                            <span class="font-semibold text-[#1D4ED8]">Puskesmas Gambir (1.8 km)</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Interactive Live Waste & Risk Simulator (Client-Side Reactive) -->
    <section id="simulator" class="py-16 bg-[#EAF1FF]/40 border-b border-[#DBE5F5]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-xs font-bold text-[#1D4ED8] uppercase tracking-wider">Algoritma PRD Section 9</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0B1F4B] mt-2 mb-4">
                    Kalkulator Simulasi Prediksi Limbah & Risiko Klinis
                </h2>
                <p class="text-sm text-[#52627F]">
                    Coba ubah parameter stok, laju pemakaian harian (burn rate), dan sisa hari kedaluwarsa di bawah
                    untuk melihat kalkulasi matematis secara langsung.
                </p>
            </div>

            <div class="max-w-4xl mx-auto bg-white rounded-2xl border border-[#DBE5F5] shadow-sm p-6 sm:p-8">
                <!-- Preset Buttons -->
                <div class="flex flex-wrap items-center gap-2 mb-8 pb-6 border-b border-[#DBE5F5]">
                    <span class="text-xs font-semibold text-[#52627F]">Pilih Preset Skenario:</span>
                    <button type="button" onclick="setPreset(480, 4.0, 45)"
                        class="px-3 py-1.5 text-xs font-medium rounded-md bg-blue-50 text-[#1D4ED8] hover:bg-blue-100 border border-blue-200">
                        Benchmark PRD: Ceftriaxone (480 stok, 4/hari, 45 hari)
                    </button>
                    <button type="button" onclick="setPreset(120, 1.0, 28)"
                        class="px-3 py-1.5 text-xs font-medium rounded-md bg-orange-50 text-[#D9480F] hover:bg-orange-100 border border-orange-200">
                        Kritis: Insulin Cold-Chain (120 stok, 1/hari, 28 hari)
                    </button>
                    <button type="button" onclick="setPreset(300, 8.0, 80)"
                        class="px-3 py-1.5 text-xs font-medium rounded-md bg-slate-100 text-[#0B1F4B] hover:bg-slate-200 border border-slate-300">
                        Aman: Amoxicillin (300 stok, 8/hari, 80 hari)
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Input 1: Current Stock -->
                    <div>
                        <label for="simStock" class="block text-xs font-semibold text-[#0B1F4B] mb-2">
                            Stok Fisik Saat Ini (S)
                        </label>
                        <div class="relative">
                            <input type="number" id="simStock" value="480" min="1" max="10000" oninput="calculateSim()"
                                class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">Unit</span>
                        </div>
                    </div>

                    <!-- Input 2: Daily Burn Rate -->
                    <div>
                        <label for="simBurnRate" class="block text-xs font-semibold text-[#0B1F4B] mb-2">
                            Laju Pemakaian Harian (B)
                        </label>
                        <div class="relative">
                            <input type="number" id="simBurnRate" value="4.0" step="0.5" min="0" max="500"
                                oninput="calculateSim()"
                                class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">Unit/hari</span>
                        </div>
                    </div>

                    <!-- Input 3: Days to Expiry -->
                    <div>
                        <label for="simDays" class="block text-xs font-semibold text-[#0B1F4B] mb-2">
                            Sisa Waktu Kedaluwarsa (D_exp)
                        </label>
                        <div class="relative">
                            <input type="number" id="simDays" value="45" min="1" max="730" oninput="calculateSim()"
                                class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">Hari</span>
                        </div>
                    </div>
                </div>

                <!-- Simulation Output Matrix -->
                <div class="bg-slate-50 border border-[#DBE5F5] rounded-xl p-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#52627F] mb-4">Hasil Evaluasi Algoritma:
                    </h4>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="p-3 bg-white rounded-lg border border-[#DBE5F5]">
                            <span class="block text-[11px] text-[#52627F]">Hari Habis (D_dep):</span>
                            <span id="outDeplete" class="text-lg font-mono font-bold text-[#0B1F4B]">120.0 hari</span>
                        </div>
                        <div class="p-3 bg-white rounded-lg border border-[#DBE5F5]">
                            <span class="block text-[11px] text-[#52627F]">Proyeksi Terpakai:</span>
                            <span id="outConsumed" class="text-lg font-mono font-bold text-[#0B1F4B]">180 unit</span>
                        </div>
                        <div class="p-3 bg-white rounded-lg border border-[#DBE5F5]">
                            <span class="block text-[11px] text-[#52627F]">Proyeksi Limbah (W):</span>
                            <span id="outWaste" class="text-lg font-mono font-bold text-[#D9480F]">300 unit</span>
                        </div>
                        <div class="p-3 bg-white rounded-lg border border-[#DBE5F5]">
                            <span class="block text-[11px] text-[#52627F]">Rasio Limbah:</span>
                            <span id="outRatio" class="text-lg font-mono font-bold text-[#D9480F]">62.5%</span>
                        </div>
                    </div>

                    <!-- Risk Evaluation Outcome -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white rounded-lg border border-[#DBE5F5] gap-4">
                        <div>
                            <span class="text-xs text-[#52627F] block">Klasifikasi Tingkat Risiko Klinis:</span>
                            <span id="outRiskBadge"
                                class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-orange-100 text-[#D9480F] border border-orange-300">
                                [HIGH RISK: 300 unit proyeksi terbuang]
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-[#52627F] block">Rekomendasi Tindakan:</span>
                            <span id="outAction" class="text-xs font-bold text-[#1D4ED8]">
                                Wajib diajukan redistribusi ke faskes sekunder
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Core Architectural Principles (Section PRD) -->
    <section id="prinsip" class="py-16 bg-white border-b border-[#DBE5F5]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <span class="text-xs font-bold text-[#1D4ED8] uppercase tracking-wider">Tata Kelola & Kepatuhan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0B1F4B] mt-2 mb-4">
                    4 Batasan Arsitektur & Prinsip Regulasi
                </h2>
                <p class="text-sm text-[#52627F]">
                    MediLoop dirancang secara khusus untuk memenuhi standar distribusi kefarmasian Indonesia tanpa celah
                    komersialisasi liar atau pelanggaran data pribadi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Principle 1 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div
                        class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        01
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Bukan Marketplace Terbuka (Decision Support
                        Only)</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        Sistem tidak melakukan transaksi otomatis atau jual-beli bebas. MediLoop bertindak murni sebagai
                        pendukung keputusan berbasis data. Setiap usulan redistribusi hibah wajib mendapatkan
                        persetujuan manual bertingkat dari Kepala Instalasi Farmasi dan Direktur Faskes terkait.
                    </p>
                </div>

                <!-- Principle 2 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div
                        class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        02
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Nol Data Pribadi Pasien (Zero PHI)</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        Sistem sama sekali tidak menyimpan rekam medis, identitas pasien, atau resep individual. Data
                        yang diproses murni berupa agregat logistik: nomor batch pabrik, tanggal kedaluwarsa, dan
                        rata-rata pengeluaran stok per hari sesuai UU Perlindungan Data Pribadi (UU PDP).
                    </p>
                </div>

                <!-- Principle 3 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div
                        class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        03
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Pencocokan Cerdas & Pembatas Serapan (Intake
                        Cap)</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        Kandidat penerima dihitung menggunakan rumus Jarak Haversine (30%), Kebutuhan Riil (50%), dan
                        Kesiapan Cold-Chain (20%). Dilengkapi algoritma pembatas jumlah serapan agar obat yang
                        dipindahkan tidak mengalami kedaluwarsa sekunder di tempat penerima.
                    </p>
                </div>

                <!-- Principle 4 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div
                        class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        04
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Invarian Stok Non-Destruktif</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        Saldo persediaan donor tidak pernah dipotong selama tahap pengusulan, persetujuan, atau
                        perjalanan fisik kurir. Pengurangan stok donor dan penambahan stok penerima terjadi secara
                        atomik HANYA saat obat diverifikasi secara fisik oleh penanggung jawab penerima.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Demo Portal -->
    <section class="py-16 bg-[#0B1F4B] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-4">
                Siap Menguji Alur Redistribusi Obat Secara Lengkap?
            </h2>
            <p class="text-sm text-blue-200/90 max-w-2xl mx-auto mb-8">
                Gunakan akun demo pra-konfigurasi untuk menguji alur pengusulan dari RSUD Tarakan, persetujuan Kepala
                Farmasi, penerimaan Puskesmas Gambir, hingga audit pengawasan Dinkes DKI Jakarta.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('demo.switch', 'donor-staff') }}"
                    class="px-6 py-3 rounded-lg bg-[#1D4ED8] hover:bg-blue-600 font-bold text-sm text-white shadow-lg transition">
                    Masuk Sebagai Staf RSUD Tarakan (Donor)
                </a>
                <a href="{{ route('demo.switch', 'auditor') }}"
                    class="px-6 py-3 rounded-lg bg-white/10 hover:bg-white/20 font-semibold text-sm text-white border border-white/20 transition">
                    Masuk Sebagai Pengawas Dinkes DKI
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#DBE5F5] py-6 text-center text-xs text-[#52627F]">
        <p><strong>MediLoop</strong> — Dikembangkan untuk Kompetisi Inovasi Layanan Kesehatan Digital 2026.</p>
        <p class="mt-1 font-mono text-[11px]">Bebas ketergantungan framework eksternal pada kalkulasi domain • WCAG 2.2
            Level AA</p>
    </footer>

    <!-- Interactive Simulator Script (Vanilla JS) -->
    <script>
        function setPreset(stock, burn, days) {
            document.getElementById('simStock').value = stock;
            document.getElementById('simBurnRate').value = burn;
            document.getElementById('simDays').value = days;
            calculateSim();
        }

        function calculateSim() {
            const stock = Math.max(0, parseFloat(document.getElementById('simStock').value) || 0);
            const burn = Math.max(0, parseFloat(document.getElementById('simBurnRate').value) || 0);
            const days = Math.max(0, parseFloat(document.getElementById('simDays').value) || 0);

            const daysToDeplete = burn > 0 ? (stock / burn).toFixed(1) : 'Tak Hingga';
            const expectedConsumption = Math.floor(burn * days);
            const projectedWaste = Math.max(0, stock - expectedConsumption);
            const wasteRatio = stock > 0 ? ((projectedWaste / stock) * 100).toFixed(1) : '0.0';

            document.getElementById('outDeplete').textContent = daysToDeplete + (burn > 0 ? ' hari' : '');
            document.getElementById('outConsumed').textContent = expectedConsumption + ' unit';
            document.getElementById('outWaste').textContent = projectedWaste + ' unit';
            document.getElementById('outRatio').textContent = wasteRatio + '%';

            const badge = document.getElementById('outRiskBadge');
            const action = document.getElementById('outAction');

            if (projectedWaste === 0) {
                badge.className = 'inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-slate-100 text-slate-700 border border-slate-300';
                badge.textContent = '[LOW: Konsumsi Cukup Sebelum Kedaluwarsa]';
                action.textContent = 'Stok aman, tidak memerlukan tindakan redistribusi.';
                action.className = 'text-xs font-bold text-slate-700';
            } else if (days <= 30) {
                badge.className = 'inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-red-100 text-[#D9480F] border border-red-300';
                badge.textContent = '[CRITICAL: Kedaluwarsa <= 30 Hari, ' + projectedWaste + ' unit terancam musnah]';
                action.textContent = 'Mendesak! Segera koordinasikan transfer hari ini.';
                action.className = 'text-xs font-bold text-[#D9480F]';
            } else if (days <= 60 || parseFloat(wasteRatio) >= 25) {
                badge.className = 'inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-orange-100 text-[#D9480F] border border-orange-300';
                badge.textContent = '[HIGH RISK: ' + projectedWaste + ' unit (' + wasteRatio + '%) proyeksi limbah]';
                action.textContent = 'Wajib diajukan usulan redistribusi ke faskes kandidat.';
                action.className = 'text-xs font-bold text-[#1D4ED8]';
            } else {
                badge.className = 'inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-amber-100 text-[#B7791F] border border-amber-300';
                badge.textContent = '[MEDIUM: ' + projectedWaste + ' unit proyeksi sisa]';
                action.textContent = 'Pantau peningkatan laju konsumsi mingguan.';
                action.className = 'text-xs font-bold text-[#B7791F]';
            }
        }
    </script>
</body>

</html>