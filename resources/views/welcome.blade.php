<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MediLoop — Platform Pendukung Keputusan Redistribusi Obat Antarfaskes</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
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
        @keyframes floatBigCapsule {
            0%, 100% {
                transform: translateY(0px) rotate(-14deg);
            }
            50% {
                transform: translateY(-22px) rotate(-9deg);
            }
        }
        @keyframes shadowBigPulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.32;
            }
            50% {
                transform: scale(0.65);
                opacity: 0.12;
            }
        }
        @keyframes floatChip1 {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-11px);
            }
        }
        @keyframes floatChip2 {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(13px);
            }
        }
        @keyframes floatChip3 {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-9px);
            }
        }
        @keyframes floatOrb1 {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-14px) rotate(22deg);
            }
        }
        @keyframes floatOrb2 {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(12px) rotate(-18deg);
            }
        }
        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(0.92);
                opacity: 0.35;
            }
            50% {
                transform: scale(1.18);
                opacity: 0.65;
            }
        }
        @keyframes sparkle {
            0%, 100% {
                opacity: 0.25;
                transform: scale(0.85);
            }
            50% {
                opacity: 1;
                transform: scale(1.25);
            }
        }
        @keyframes orbitRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .mediloop-big-capsule {
            animation: floatBigCapsule 4.5s ease-in-out infinite;
            transform-origin: center center;
            will-change: transform;
        }
        .mediloop-big-shadow {
            animation: shadowBigPulse 4.5s ease-in-out infinite;
            transform-origin: center center;
            will-change: transform, opacity;
        }
        .mediloop-chip-1 {
            animation: floatChip1 3.8s ease-in-out infinite;
            will-change: transform;
        }
        .mediloop-chip-2 {
            animation: floatChip2 4.6s ease-in-out infinite;
            will-change: transform;
        }
        .mediloop-chip-3 {
            animation: floatChip3 4.1s ease-in-out infinite;
            will-change: transform;
        }
        .mediloop-pill-float-1 {
            animation: floatOrb1 3.6s ease-in-out infinite;
            will-change: transform;
        }
        .mediloop-pill-float-2 {
            animation: floatOrb2 4.2s ease-in-out infinite;
            will-change: transform;
        }
        .mediloop-glow {
            animation: pulseGlow 5s ease-in-out infinite;
        }
        .mediloop-sparkle {
            animation: sparkle 2.6s ease-in-out infinite;
        }
        .mediloop-orbit-ring {
            animation: orbitRotate 40s linear infinite;
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 antialiased selection:bg-blue-100 selection:text-blue-900">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-[#DBE5F5] sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-[#1D4ED8] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-[#0B1F4B]">MediLoop</span>
                    <span class="hidden md:inline-block ml-2 px-2 py-0.5 text-[10px] font-semibold text-blue-800 bg-[#EAF1FF] rounded border border-[#DBE5F5]">Decision Support Only</span>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                <a href="#simulator" class="hidden sm:inline-block text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] px-3 py-2">
                    Kalkulator Simulasi
                </a>
                <a href="#prinsip" class="hidden sm:inline-block text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] px-3 py-2">
                    Prinsip Sistem
                </a>
                <a href="{{ route('demo.switch', 'donor-staff') }}" class="px-4 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                    Buka Dashboard Demo
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
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF1FF] border border-[#DBE5F5] text-xs font-semibold text-[#1D4ED8] mb-6">
                        <span class="w-2 h-2 rounded-full bg-[#1D4ED8]"></span>
                        Sistem Pendukung Keputusan Hibah & Redistribusi Obat Antarfaskes
                    </div>
                    
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B1F4B] tracking-tight leading-tight mb-6">
                        Cegah Pemusnahan Obat Kedaluwarsa Melalui Koordinasi Antarfaskes.
                    </h1>
                    
                    <p class="text-base sm:text-lg text-[#52627F] leading-relaxed mb-8">
                        MediLoop mendeteksi surplus obat berisiko kedaluwarsa di rumah sakit dan mencocokkannya dengan kebutuhan riil puskesmas terdekat secara terukur, aman, dan patuh regulasi Kemenkes.
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('demo.switch', 'donor-staff') }}" class="px-5 py-3 text-sm font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition inline-flex items-center gap-2">
                            <span>Mulai Simulasi Demo</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="#simulator" class="px-5 py-3 text-sm font-semibold text-[#0B1F4B] bg-slate-100 hover:bg-slate-200 rounded-lg transition border border-[#DBE5F5]">
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

                <!-- Right: Free-Floating Giant 3D Medicine Hero Art with Floating Glass Metrics -->
                <div class="lg:col-span-5 flex justify-center items-center relative py-6">
                    <div class="relative w-full max-w-lg h-[460px] flex items-center justify-center select-none" id="hero-interactive-stage">
                        
                        <!-- Multi-Layer Radiant Ambient Glow -->
                        <div class="absolute w-80 h-80 bg-gradient-to-tr from-blue-600/25 via-indigo-500/20 to-cyan-300/30 rounded-full blur-3xl pointer-events-none mediloop-glow"></div>
                        <div class="absolute w-60 h-60 bg-blue-400/20 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- Faint Orbital Ring (Adds futuristic space/depth) -->
                        <div class="absolute w-[360px] h-[360px] rounded-full border border-blue-200/40 border-dashed mediloop-orbit-ring pointer-events-none"></div>

                        <!-- Sparkle Highlights -->
                        <div class="absolute top-6 left-12 text-blue-500/80 mediloop-sparkle text-lg font-bold select-none">+</div>
                        <div class="absolute bottom-16 right-8 text-cyan-400/90 mediloop-sparkle text-sm font-bold select-none" style="animation-delay: 1.5s;">+</div>
                        <div class="absolute top-12 right-12 text-indigo-400/70 mediloop-sparkle text-xs font-bold select-none" style="animation-delay: 0.8s;">+</div>

                        <!-- FLOATING GLASS CHIP 1: Top Left (Redistribution Status & Batch Name) -->
                        <div class="mediloop-chip-1 absolute top-2 left-0 sm:-left-4 z-30 bg-white/85 hover:bg-white backdrop-blur-md border border-white/70 shadow-lg rounded-2xl px-4 py-2.5 transition transform hover:scale-105 cursor-default">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center text-[#D9480F] font-bold text-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-extrabold text-[#0B1F4B]">Ceftriaxone 1g</span>
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    </div>
                                    <div class="text-[10.5px] font-bold text-[#D9480F] flex items-center gap-1">
                                        <span>Surplus 300 Unit</span>
                                        <span class="text-slate-400">•</span>
                                        <span class="text-slate-500 font-normal">Sisa 45 Hari</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FLOATING ROUND SCORED TABLET (Top Right) -->
                        <div class="mediloop-pill-float-1 absolute top-6 right-4 sm:right-6 z-20 cursor-pointer" title="Paracetamol / Scored Tablet">
                            <svg width="62" height="62" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="filter drop-shadow-xl">
                                <circle cx="30" cy="30" r="26" fill="url(#heroTabGrad)" stroke="#CBD5E1" stroke-width="2.5"/>
                                <line x1="30" y1="10" x2="30" y2="50" stroke="#94A3B8" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="30" cy="30" r="23" fill="none" stroke="white" stroke-opacity="0.8" stroke-width="1.5"/>
                                <defs>
                                    <linearGradient id="heroTabGrad" x1="10" y1="10" x2="50" y2="50" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="55%" stop-color="#F8FAFC"/>
                                        <stop offset="100%" stop-color="#C7D7F5"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>

                        <!-- FLOATING MINI AMBER VITAMIN CAPSULE (Mid-Left) -->
                        <div class="mediloop-pill-float-2 absolute bottom-24 -left-2 sm:left-2 z-20 cursor-pointer" title="Kapsul Suplemen">
                            <svg width="54" height="34" viewBox="0 0 52 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="filter drop-shadow-lg transform -rotate-25">
                                <rect x="2" y="2" width="48" height="28" rx="14" fill="#FEF3C7" stroke="#F59E0B" stroke-width="2.5"/>
                                <path d="M26 2 L38 2 C44 2 48 8 48 16 C48 24 44 30 38 30 L26 30 Z" fill="#F59E0B"/>
                                <line x1="26" y1="2" x2="26" y2="30" stroke="#B45309" stroke-width="2"/>
                                <path d="M8 6 C5 9 5 13 5 16 C8 10 14 8 22 8 L36 8 C40 8 42 10 44 12 C42 8 36 6 30 6 L16 6 Z" fill="white" fill-opacity="0.6"/>
                            </svg>
                        </div>

                        <!-- GIANT 3D HERO CAPSULE (Centerpiece Anchor) -->
                        <div class="mediloop-big-capsule absolute z-20 cursor-pointer">
                            <svg width="340" height="150" viewBox="0 0 340 150" fill="none" xmlns="http://www.w3.org/2000/svg" class="filter drop-shadow-2xl">
                                <defs>
                                    <!-- Shell Left Gradient: Pearl Frost White -->
                                    <linearGradient id="bigCapLeft" x1="20" y1="20" x2="20" y2="130" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="35%" stop-color="#F8FAFC"/>
                                        <stop offset="75%" stop-color="#E2E8F0"/>
                                        <stop offset="100%" stop-color="#94A3B8"/>
                                    </linearGradient>

                                    <!-- Shell Right Gradient: Deep Royal Sapphire -->
                                    <linearGradient id="bigCapRight" x1="170" y1="20" x2="170" y2="130" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#60A5FA"/>
                                        <stop offset="25%" stop-color="#2563EB"/>
                                        <stop offset="65%" stop-color="#1D4ED8"/>
                                        <stop offset="100%" stop-color="#0B1F4B"/>
                                    </linearGradient>

                                    <!-- Cylindrical Glass Highlight -->
                                    <linearGradient id="bigCapGloss" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.9"/>
                                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0"/>
                                    </linearGradient>

                                    <!-- Metallic Center Seam Band -->
                                    <linearGradient id="bigCapBand" x1="167" y1="20" x2="173" y2="130" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#BFDBFE"/>
                                        <stop offset="30%" stop-color="#FFFFFF"/>
                                        <stop offset="70%" stop-color="#93C5FD"/>
                                        <stop offset="100%" stop-color="#1D4ED8"/>
                                    </linearGradient>

                                    <!-- Soft Blue Ambient Light on Bottom Belly -->
                                    <radialGradient id="bigCapBelly" cx="50%" cy="100%" r="60%">
                                        <stop offset="0%" stop-color="#60A5FA" stop-opacity="0.4"/>
                                        <stop offset="100%" stop-color="#60A5FA" stop-opacity="0"/>
                                    </radialGradient>
                                </defs>

                                <g>
                                    <!-- Left Shell (White Pearl) -->
                                    <path d="M85 24 C38 24 22 48 22 75 C22 102 38 126 85 126 L170 126 L170 24 Z" fill="url(#bigCapLeft)" stroke="#94A3B8" stroke-width="2"/>

                                    <!-- Right Shell (Royal Sapphire) -->
                                    <path d="M170 24 L255 24 C302 24 318 48 318 75 C318 102 302 126 255 126 L170 126 Z" fill="url(#bigCapRight)" stroke="#1E40AF" stroke-width="2"/>

                                    <!-- Ambient Belly Bounce Light -->
                                    <rect x="22" y="24" width="296" height="102" rx="51" fill="url(#bigCapBelly)" style="mix-blend-mode: overlay;"/>

                                    <!-- Middle Connection Seam Ring -->
                                    <line x1="170" y1="23" x2="170" y2="127" stroke="url(#bigCapBand)" stroke-width="6.5" stroke-linecap="round"/>

                                    <!-- Upper 3D Specular Curved Reflection (Glass Reflection) -->
                                    <path d="M55 35 C40 43 32 54 32 68 C48 54 80 46 150 46 L255 46 C275 46 295 50 305 60 C300 45 282 35 255 35 Z" fill="url(#bigCapGloss)"/>

                                    <!-- Subtle Embossed Cross Emblem on Blue Shell -->
                                    <g opacity="0.85">
                                        <rect x="232" y="69" width="26" height="11" rx="4" fill="#FFFFFF"/>
                                        <rect x="239.5" y="61.5" width="11" height="26" rx="4" fill="#FFFFFF"/>
                                    </g>

                                    <!-- Subtle Embossed Cross Emblem on White Shell -->
                                    <g opacity="0.65">
                                        <rect x="74" y="69" width="26" height="11" rx="4" fill="#1D4ED8"/>
                                        <rect x="81.5" y="61.5" width="11" height="26" rx="4" fill="#1D4ED8"/>
                                    </g>
                                </g>
                            </svg>
                        </div>

                        <!-- Dynamic Ground Floor Shadow -->
                        <div class="mediloop-big-shadow absolute bottom-6 w-72 h-7 bg-blue-950/25 rounded-full blur-md"></div>

                        <!-- FLOATING GLASS CHIP 2: Bottom Right (Nearest Matching Recommendation) -->
                        <div class="mediloop-chip-2 absolute bottom-2 -right-2 sm:-right-4 z-30 bg-white/85 hover:bg-white backdrop-blur-md border border-white/70 shadow-lg rounded-2xl px-4 py-2.5 transition transform hover:scale-105 cursor-default">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center text-[#1D4ED8] font-bold text-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Rekomendasi Match</div>
                                    <div class="text-xs font-extrabold text-[#0B1F4B]">Puskesmas Gambir</div>
                                    <div class="text-[10.5px] font-semibold text-blue-700 flex items-center gap-1">
                                        <span>1.8 km</span>
                                        <span class="text-slate-400">•</span>
                                        <span class="text-cyan-700 font-bold">Cold-Chain Ready</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FLOATING GLASS CHIP 3: Bottom Left (Algorithm Score) -->
                        <div class="mediloop-chip-3 absolute bottom-6 left-2 sm:-left-2 z-30 bg-white/85 hover:bg-white backdrop-blur-md border border-white/70 shadow-lg rounded-2xl px-3.5 py-2 transition transform hover:scale-105 cursor-default hidden sm:flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <div class="text-xs font-extrabold text-[#0B1F4B]">92% Match Score</div>
                                <div class="text-[10px] text-slate-500 font-mono">BATCH: CFT-2026-X89</div>
                            </div>
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
                    Coba ubah parameter stok, laju pemakaian harian (burn rate), dan sisa hari kedaluwarsa di bawah untuk melihat kalkulasi matematis secara langsung.
                </p>
            </div>

            <div class="max-w-4xl mx-auto bg-white rounded-2xl border border-[#DBE5F5] shadow-sm p-6 sm:p-8">
                <!-- Preset Buttons -->
                <div class="flex flex-wrap items-center gap-2 mb-8 pb-6 border-b border-[#DBE5F5]">
                    <span class="text-xs font-semibold text-[#52627F]">Pilih Preset Skenario:</span>
                    <button type="button" onclick="setPreset(480, 4.0, 45)" class="px-3 py-1.5 text-xs font-medium rounded-md bg-blue-50 text-[#1D4ED8] hover:bg-blue-100 border border-blue-200">
                        Benchmark PRD: Ceftriaxone (480 stok, 4/hari, 45 hari)
                    </button>
                    <button type="button" onclick="setPreset(120, 1.0, 28)" class="px-3 py-1.5 text-xs font-medium rounded-md bg-orange-50 text-[#D9480F] hover:bg-orange-100 border border-orange-200">
                        Kritis: Insulin Cold-Chain (120 stok, 1/hari, 28 hari)
                    </button>
                    <button type="button" onclick="setPreset(300, 8.0, 80)" class="px-3 py-1.5 text-xs font-medium rounded-md bg-slate-100 text-[#0B1F4B] hover:bg-slate-200 border border-slate-300">
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
                            <input type="number" id="simStock" value="480" min="1" max="10000" oninput="calculateSim()" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">Unit</span>
                        </div>
                    </div>

                    <!-- Input 2: Daily Burn Rate -->
                    <div>
                        <label for="simBurnRate" class="block text-xs font-semibold text-[#0B1F4B] mb-2">
                            Laju Pemakaian Harian (B)
                        </label>
                        <div class="relative">
                            <input type="number" id="simBurnRate" value="4.0" step="0.5" min="0" max="500" oninput="calculateSim()" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">Unit/hari</span>
                        </div>
                    </div>

                    <!-- Input 3: Days to Expiry -->
                    <div>
                        <label for="simDays" class="block text-xs font-semibold text-[#0B1F4B] mb-2">
                            Sisa Waktu Kedaluwarsa (D_exp)
                        </label>
                        <div class="relative">
                            <input type="number" id="simDays" value="45" min="1" max="730" oninput="calculateSim()" class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">Hari</span>
                        </div>
                    </div>
                </div>

                <!-- Simulation Output Matrix -->
                <div class="bg-slate-50 border border-[#DBE5F5] rounded-xl p-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#52627F] mb-4">Hasil Evaluasi Algoritma:</h4>
                    
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
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white rounded-lg border border-[#DBE5F5] gap-4">
                        <div>
                            <span class="text-xs text-[#52627F] block">Klasifikasi Tingkat Risiko Klinis:</span>
                            <span id="outRiskBadge" class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-orange-100 text-[#D9480F] border border-orange-300">
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
                    MediLoop dirancang secara khusus untuk memenuhi standar distribusi kefarmasian Indonesia tanpa celah komersialisasi liar atau pelanggaran data pribadi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Principle 1 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        01
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Bukan Marketplace Terbuka (Decision Support Only)</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        Sistem tidak melakukan transaksi otomatis atau jual-beli bebas. MediLoop bertindak murni sebagai pendukung keputusan berbasis data. Setiap usulan redistribusi hibah wajib mendapatkan persetujuan manual bertingkat dari Kepala Instalasi Farmasi dan Direktur Faskes terkait.
                    </p>
                </div>

                <!-- Principle 2 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        02
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Nol Data Pribadi Pasien (Zero PHI)</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        Sistem sama sekali tidak menyimpan rekam medis, identitas pasien, atau resep individual. Data yang diproses murni berupa agregat logistik: nomor batch pabrik, tanggal kedaluwarsa, dan rata-rata pengeluaran stok per hari sesuai UU Perlindungan Data Pribadi (UU PDP).
                    </p>
                </div>

                <!-- Principle 3 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        03
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Pencocokan Cerdas & Pembatas Serapan (Intake Cap)</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        Kandidat penerima dihitung menggunakan rumus Jarak Haversine (30%), Kebutuhan Riil (50%), dan Kesiapan Cold-Chain (20%). Dilengkapi algoritma pembatas jumlah serapan agar obat yang dipindahkan tidak mengalami kedaluwarsa sekunder di tempat penerima.
                    </p>
                </div>

                <!-- Principle 4 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        04
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">Invarian Stok Non-Destruktif</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        Saldo persediaan donor tidak pernah dipotong selama tahap pengusulan, persetujuan, atau perjalanan fisik kurir. Pengurangan stok donor dan penambahan stok penerima terjadi secara atomik HANYA saat obat diverifikasi secara fisik oleh penanggung jawab penerima.
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
                Gunakan akun demo pra-konfigurasi untuk menguji alur pengusulan dari RSUD Tarakan, persetujuan Kepala Farmasi, penerimaan Puskesmas Gambir, hingga audit pengawasan Dinkes DKI Jakarta.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('demo.switch', 'donor-staff') }}" class="px-6 py-3 rounded-lg bg-[#1D4ED8] hover:bg-blue-600 font-bold text-sm text-white shadow-lg transition">
                    Masuk Sebagai Staf RSUD Tarakan (Donor)
                </a>
                <a href="{{ route('demo.switch', 'auditor') }}" class="px-6 py-3 rounded-lg bg-white/10 hover:bg-white/20 font-semibold text-sm text-white border border-white/20 transition">
                    Masuk Sebagai Pengawas Dinkes DKI
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#DBE5F5] py-6 text-center text-xs text-[#52627F]">
        <p><strong>MediLoop</strong> — Dikembangkan untuk Kompetisi Inovasi Layanan Kesehatan Digital 2026.</p>
        <p class="mt-1 font-mono text-[11px]">Bebas ketergantungan framework eksternal pada kalkulasi domain • WCAG 2.2 Level AA</p>
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

        // Interactive 3D Tilt for Hero Medicine Card
        document.addEventListener('DOMContentLoaded', function () {
            const heroCard = document.querySelector('.card-tilt-hover');
            const capsule = document.querySelector('.mediloop-capsule-float');
            if (heroCard && capsule) {
                heroCard.addEventListener('mousemove', function (e) {
                    const rect = heroCard.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    const rotateX = -(y / rect.height) * 15;
                    const rotateY = (x / rect.width) * 15;
                    capsule.style.transform = `translateY(-12px) perspective(600px) rotateX(${rotateX.toFixed(1)}deg) rotateY(${rotateY.toFixed(1)}deg) rotate(-8deg)`;
                });
                heroCard.addEventListener('mouseleave', function () {
                    capsule.style.transform = '';
                });
            }
        });
    </script>
</body>
</html>
