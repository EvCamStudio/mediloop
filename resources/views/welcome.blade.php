<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('landing.meta_description') }}">
    <title>{{ __('landing.title') }}</title>
    @include('partials.favicons')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/json" id="js-translations">@json(__('js'))</script>
    <style>
        :root {
            --brand-primary: #1D4ED8;
            --brand-navy: #0B1F4B;
            --brand-sky: #EAF1FF;
            --brand-border: #DBE5F5;
            --brand-muted: #52627F;
            --brand-risk: #B33A0A;
            --brand-warning: #B7791F;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: #F8FAFC;
            color: #0B1F4B;
        }

        /* Hero illustration tokens */
        :root {
            --hero-capsule-white: #FFFFFF;
            --hero-capsule-frost: #F8FAFC;
            --hero-capsule-silver: #E2E8F0;
            --hero-capsule-steel: #94A3B8;
            --hero-capsule-sky: #60A5FA;
            --hero-capsule-blue: #2563EB;
            --hero-capsule-deep: #1E40AF;
            --hero-capsule-pale: #BFDBFE;
            --hero-capsule-mist: #93C5FD;
            --hero-tablet-blue: #C7D7F5;
            --hero-amber-soft: #FEF3C7;
            --hero-amber: #F59E0B;
            --hero-amber-deep: #B45309;
        }

        @keyframes floatBigCapsule {
            0%, 100% { transform: translateY(0) rotate(-14deg); }
            50% { transform: translateY(-22px) rotate(-9deg); }
        }
        @keyframes shadowBigPulse {
            0%, 100% { transform: scale(1); opacity: 0.32; }
            50% { transform: scale(0.65); opacity: 0.12; }
        }
        @keyframes floatChip1 {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-11px); }
        }
        @keyframes floatChip2 {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(13px); }
        }
        @keyframes floatChip3 {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-9px); }
        }
        @keyframes floatOrb1 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-14px) rotate(22deg); }
        }
        @keyframes floatOrb2 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(12px) rotate(-18deg); }
        }
        @keyframes pulseGlow {
            0%, 100% { transform: scale(0.92); opacity: 0.35; }
            50% { transform: scale(1.18); opacity: 0.65; }
        }
        @keyframes sparkle {
            0%, 100% { opacity: 0.25; transform: scale(0.85); }
            50% { opacity: 1; transform: scale(1.25); }
        }
        @keyframes orbitRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes pulseDot {
            0%, 100% { transform: scale(0.85); opacity: 0.55; }
            50% { transform: scale(1.25); opacity: 1; }
        }
        .ml-hero-stage {
            isolation: isolate;
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
        .mediloop-pulse-dot {
            animation: pulseDot 1.8s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .ml-hero-stage *,
            .ml-hero-stage *::before,
            .ml-hero-stage *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body
    class="min-h-full flex flex-col bg-slate-50 text-slate-900 antialiased selection:bg-blue-100 selection:text-blue-900">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-[#DBE5F5] sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="MediLoop" class="w-10 h-10 object-contain" />
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-[#0B1F4B]">MediLoop</span>
                    <span
                        class="hidden md:inline-block ml-2 px-2 py-0.5 text-[10px] font-semibold text-blue-800 bg-[#EAF1FF] rounded border border-[#DBE5F5]">{{ __('landing.top_badge') }}</span>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                <a href="#simulator"
                    class="hidden sm:inline-block text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] px-3 py-2">
                    {{ __('landing.nav.simulator') }}
                </a>
                <a href="#prinsip"
                    class="hidden sm:inline-block text-xs font-semibold text-[#52627F] hover:text-[#0B1F4B] px-3 py-2">
                    {{ __('landing.nav.principles') }}
                </a>
                <a href="{{ route('login') }}"
                    class="px-4 py-2 text-xs font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition">
                    {{ __('landing.nav.open_dashboard') }}
                </a>
            </nav>
        </div>
    </header>

    <main id="main-content" class="flex-1">
    <!-- Hero Section with 3D Capsule Vector Focal Anchor -->
    <section class="relative bg-white border-b border-[#DBE5F5] py-16 lg:py-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                <!-- Left: Clinical Value Proposition -->
                <div class="lg:col-span-7">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF1FF] border border-[#DBE5F5] text-xs font-semibold text-[#1D4ED8] mb-6">
                        <span class="w-2 h-2 rounded-full bg-[#1D4ED8]"></span>
                        {{ __('landing.hero.badge') }}
                    </div>

                    <h1
                        class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B1F4B] tracking-tight leading-tight mb-6">
                        {{ __('landing.hero.title') }}
                    </h1>

                    <p class="text-base sm:text-lg text-[#52627F] leading-relaxed mb-8">
                        {{ __('landing.hero.body') }}
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('login') }}"
                            class="px-5 py-3 text-sm font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-lg shadow-xs transition inline-flex items-center gap-2">
                            <span>{{ __('landing.hero.cta_simulation') }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="#simulator"
                            class="px-5 py-3 text-sm font-semibold text-[#0B1F4B] bg-slate-100 hover:bg-slate-200 rounded-lg transition border border-[#DBE5F5]">
                            {{ __('landing.hero.cta_algorithm') }}
                        </a>
                    </div>

                    <!-- Hard Operational Constraints (No PHI, Decision-Support Only) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-10 pt-8 border-t border-[#DBE5F5] text-xs">
                        <div>
                            <span class="block font-bold text-[#0B1F4B]">{{ __('landing.hero.phi_title') }}</span>
                            <span class="text-[#52627F]">{{ __('landing.hero.phi_body') }}</span>
                        </div>
                        <div>
                            <span class="block font-bold text-[#0B1F4B]">{{ __('landing.hero.approval_title') }}</span>
                            <span class="text-[#52627F]">{{ __('landing.hero.approval_body') }}</span>
                        </div>
                        <div>
                            <span class="block font-bold text-[#0B1F4B]">{{ __('landing.hero.invariant_title') }}</span>
                            <span class="text-[#52627F]">{{ __('landing.hero.invariant_body') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Animated pharmaceutical hero illustration (simulated PRD case) -->
                <div class="lg:col-span-5 flex justify-center items-center relative py-6">
                    <div class="w-full max-w-lg">
                        <div class="ml-hero-stage relative flex h-[380px] w-full select-none items-center justify-center sm:h-[460px]"
                            id="hero-interactive-stage">
                            <!-- Ambient glow and orbital depth -->
                            <div
                                class="mediloop-glow pointer-events-none absolute h-64 w-64 rounded-full bg-gradient-to-tr from-blue-600/25 via-indigo-500/20 to-cyan-300/30 blur-3xl sm:h-80 sm:w-80"
                                aria-hidden="true"></div>
                            <div class="pointer-events-none absolute h-48 w-48 rounded-full bg-blue-400/20 blur-2xl sm:h-60 sm:w-60"
                                aria-hidden="true"></div>
                            <div
                                class="mediloop-orbit-ring pointer-events-none absolute h-[300px] w-[300px] rounded-full border border-dashed border-blue-200/40 sm:h-[360px] sm:w-[360px]"
                                aria-hidden="true"></div>

                            <!-- Sparkle highlights -->
                            <div class="mediloop-sparkle absolute left-10 top-5 select-none text-lg font-bold text-blue-500/80"
                                aria-hidden="true">+</div>
                            <div class="mediloop-sparkle absolute bottom-16 right-7 select-none text-sm font-bold text-cyan-400/90"
                                style="animation-delay: 1.5s;" aria-hidden="true">+</div>
                            <div class="mediloop-sparkle absolute right-10 top-11 select-none text-xs font-bold text-indigo-400/70"
                                style="animation-delay: 0.8s;" aria-hidden="true">+</div>

                            <!-- Floating chip: batch status -->
                            <div
                                class="mediloop-chip-1 absolute left-0 top-2 z-30 rounded-2xl border border-white/70 bg-white px-4 py-2.5 shadow-lg sm:-left-4">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-[var(--brand-risk)]">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-extrabold text-[var(--brand-navy)]">Ceftriaxone 1g</span>
                                            <span class="mediloop-pulse-dot h-2 w-2 rounded-full bg-emerald-500"
                                                aria-hidden="true"></span>
                                        </div>
                                        <div class="flex items-center gap-1 text-[10.5px] font-bold text-[var(--brand-risk)]">
                                            <span>{{ __('landing.chips.waste_projection', ['count' => '300']) }}</span>
                                            <span class="text-slate-400">•</span>
                                            <span class="font-normal text-slate-600">{{ __('landing.chips.remaining_days', ['days' => '45']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating scored tablet -->
                            <div class="mediloop-pill-float-1 absolute right-4 top-6 z-20 hidden sm:right-6 sm:block" aria-hidden="true">
                                <svg width="62" height="62" viewBox="0 0 60 60" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" class="filter drop-shadow-xl">
                                    <circle cx="30" cy="30" r="26" fill="url(#heroTabGrad)"
                                        stroke="var(--hero-capsule-mist)" stroke-width="2.5" />
                                    <line x1="30" y1="10" x2="30" y2="50" stroke="var(--hero-capsule-steel)"
                                        stroke-width="3" stroke-linecap="round" />
                                    <circle cx="30" cy="30" r="23" fill="none" stroke="var(--hero-capsule-white)"
                                        stroke-opacity="0.8" stroke-width="1.5" />
                                    <defs>
                                        <linearGradient id="heroTabGrad" x1="10" y1="10" x2="50" y2="50"
                                            gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="var(--hero-capsule-white)" />
                                            <stop offset="55%" stop-color="var(--hero-capsule-frost)" />
                                            <stop offset="100%" stop-color="var(--hero-tablet-blue)" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>

                            <!-- Floating mini capsule -->
                            <div class="mediloop-pill-float-2 absolute -left-2 bottom-24 z-20 sm:left-2" aria-hidden="true">
                                <svg width="54" height="34" viewBox="0 0 52 32" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" class="filter -rotate-[25deg] transform drop-shadow-lg">
                                    <rect x="2" y="2" width="48" height="28" rx="14"
                                        fill="var(--hero-amber-soft)" stroke="var(--hero-amber)" stroke-width="2.5" />
                                    <path d="M26 2 L38 2 C44 2 48 8 48 16 C48 24 44 30 38 30 L26 30 Z"
                                        fill="var(--hero-amber)" />
                                    <line x1="26" y1="2" x2="26" y2="30" stroke="var(--hero-amber-deep)"
                                        stroke-width="2" />
                                    <path
                                        d="M8 6 C5 9 5 13 5 16 C8 10 14 8 22 8 L36 8 C40 8 42 10 44 12 C42 8 36 6 30 6 L16 6 Z"
                                        fill="var(--hero-capsule-white)" fill-opacity="0.6" />
                                </svg>
                            </div>

                            <!-- Giant 3D capsule centerpiece -->
                            <div class="mediloop-big-capsule absolute z-20" aria-hidden="true">
                                <svg width="340" height="150" viewBox="0 0 340 150" fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-auto w-[290px] filter drop-shadow-2xl sm:w-[340px]">
                                    <defs>
                                        <linearGradient id="bigCapLeft" x1="20" y1="20" x2="20" y2="130"
                                            gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="var(--hero-capsule-white)" />
                                            <stop offset="35%" stop-color="var(--hero-capsule-frost)" />
                                            <stop offset="75%" stop-color="var(--hero-capsule-silver)" />
                                            <stop offset="100%" stop-color="var(--hero-capsule-steel)" />
                                        </linearGradient>
                                        <linearGradient id="bigCapRight" x1="170" y1="20" x2="170" y2="130"
                                            gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="var(--hero-capsule-sky)" />
                                            <stop offset="25%" stop-color="var(--hero-capsule-blue)" />
                                            <stop offset="65%" stop-color="var(--brand-primary)" />
                                            <stop offset="100%" stop-color="var(--brand-navy)" />
                                        </linearGradient>
                                        <linearGradient id="bigCapGloss" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="var(--hero-capsule-white)" stop-opacity="0.9" />
                                            <stop offset="100%" stop-color="var(--hero-capsule-white)" stop-opacity="0" />
                                        </linearGradient>
                                        <linearGradient id="bigCapBand" x1="167" y1="20" x2="173" y2="130"
                                            gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="var(--hero-capsule-pale)" />
                                            <stop offset="30%" stop-color="var(--hero-capsule-white)" />
                                            <stop offset="70%" stop-color="var(--hero-capsule-mist)" />
                                            <stop offset="100%" stop-color="var(--brand-primary)" />
                                        </linearGradient>
                                        <radialGradient id="bigCapBelly" cx="50%" cy="100%" r="60%">
                                            <stop offset="0%" stop-color="var(--hero-capsule-sky)" stop-opacity="0.4" />
                                            <stop offset="100%" stop-color="var(--hero-capsule-sky)" stop-opacity="0" />
                                        </radialGradient>
                                    </defs>

                                    <g>
                                        <path
                                            d="M85 24 C38 24 22 48 22 75 C22 102 38 126 85 126 L170 126 L170 24 Z"
                                            fill="url(#bigCapLeft)" stroke="var(--hero-capsule-steel)" stroke-width="2" />
                                        <path
                                            d="M170 24 L255 24 C302 24 318 48 318 75 C318 102 302 126 255 126 L170 126 Z"
                                            fill="url(#bigCapRight)" stroke="var(--hero-capsule-deep)" stroke-width="2" />
                                        <rect x="22" y="24" width="296" height="102" rx="51"
                                            fill="url(#bigCapBelly)" style="mix-blend-mode: overlay;" />
                                        <line x1="170" y1="23" x2="170" y2="127" stroke="url(#bigCapBand)"
                                            stroke-width="6.5" stroke-linecap="round" />
                                        <path
                                            d="M55 35 C40 43 32 54 32 68 C48 54 80 46 150 46 L255 46 C275 46 295 50 305 60 C300 45 282 35 255 35 Z"
                                            fill="url(#bigCapGloss)" />
                                        <g opacity="0.85">
                                            <rect x="232" y="69" width="26" height="11" rx="4"
                                                fill="var(--hero-capsule-white)" />
                                            <rect x="239.5" y="61.5" width="11" height="26" rx="4"
                                                fill="var(--hero-capsule-white)" />
                                        </g>
                                        <g opacity="0.65">
                                            <rect x="74" y="69" width="26" height="11" rx="4"
                                                fill="var(--brand-primary)" />
                                            <rect x="81.5" y="61.5" width="11" height="26" rx="4"
                                                fill="var(--brand-primary)" />
                                        </g>
                                    </g>
                                </svg>
                            </div>

                            <!-- Ground shadow -->
                            <div
                                class="mediloop-big-shadow absolute bottom-8 h-7 w-56 rounded-full bg-blue-950/25 blur-md sm:w-72"
                                aria-hidden="true"></div>

                            <!-- Floating chip: nearest recommendation -->
                            <div
                                class="mediloop-chip-2 absolute bottom-2 right-0 z-30 rounded-2xl border border-white/70 bg-white px-4 py-2.5 shadow-lg sm:-right-4">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-[var(--brand-primary)]">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-600">
                                            {{ __('landing.chips.facility_recommendation') }}
                                        </div>
                                        <div class="text-xs font-extrabold text-[var(--brand-navy)]">Puskesmas Gambir</div>
                                        <div class="flex items-center gap-1 text-[10.5px] font-semibold text-blue-700">
                                            <span>1,8 km</span>
                                            <span class="text-slate-400">•</span>
                                            <span class="font-bold text-teal-700">{{ __('landing.chips.cold_chain_ready') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating chip: simulated match score -->
                            <div
                                class="mediloop-chip-3 absolute bottom-6 left-2 z-30 hidden items-center gap-2 rounded-2xl border border-white/70 bg-white px-3.5 py-2 shadow-lg sm:-left-2 sm:flex">
                                <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <div>
                                    <div class="text-xs font-extrabold text-[var(--brand-navy)]">{{ __('landing.chips.example_score') }}</div>
                                    <div class="font-mono text-[10px] text-slate-600">BATCH: CFT-2026-X89</div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 text-center text-[11px] font-medium leading-relaxed text-slate-600">
                            {{ __('landing.chips.simulation_note') }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Interactive Live Waste & Risk Simulator (Client-Side Reactive) -->
    <section id="simulator" class="scroll-mt-20 py-16 bg-[#EAF1FF]/40 border-b border-[#DBE5F5]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-xs font-bold text-[#1D4ED8] uppercase tracking-wider">{{ __('landing.simulator.eyebrow') }}</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0B1F4B] mt-2 mb-4">
                    {{ __('landing.simulator.title') }}
                </h2>
                <p class="text-sm text-[#52627F]">
                    {{ __('landing.simulator.subtitle') }}
                </p>
            </div>

            <div class="max-w-4xl mx-auto bg-white rounded-2xl border border-[#DBE5F5] shadow-sm p-6 sm:p-8">
                <!-- Medicine Dropdown -->
                <div class="mb-8 pb-6 border-b border-[#DBE5F5]">
                    <label for="simMedicine" class="block text-xs font-semibold text-[#52627F] mb-2">{{ __('landing.simulator.medicine_select') }}</label>
                    <select id="simMedicine" onchange="onMedicineChange(this.value)" class="w-full px-3 py-2.5 text-sm border border-[#DBE5F5] rounded-lg text-[#0B1F4B] focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none bg-white">
                        <option value="">{{ __('landing.simulator.choose_medicine') }}</option>
                        <option value="ceftriaxone">Ceftriaxone 1g Injeksi (480, 4.0, 45)</option>
                        <option value="insulin">Insulin Glargine 100 IU/ml (120, 1.0, 28)</option>
                        <option value="amoxicillin">Amoxicillin 500mg Tablet (300, 8.0, 80)</option>
                        <option value="paracetamol">Paracetamol 500mg Tablet (300, 8.0, 80)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Input 1: Current Stock -->
                    <div>
                        <label for="simStock" class="block text-xs font-semibold text-[#0B1F4B] mb-2">
                            {{ __('landing.simulator.stock_label') }}
                        </label>
                        <div class="relative">
                            <input type="number" id="simStock" value="480" min="1" max="10000" oninput="calculateSim()"
                                class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">{{ __('landing.simulator.unit') }}</span>
                        </div>
                    </div>

                    <!-- Input 2: Daily Burn Rate -->
                    <div>
                        <label for="simBurnRate" class="block text-xs font-semibold text-[#0B1F4B] mb-2">
                            {{ __('landing.simulator.burn_label') }}
                        </label>
                        <div class="relative">
                            <input type="number" id="simBurnRate" value="4.0" step="0.5" min="0" max="500"
                                oninput="calculateSim()"
                                class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">{{ __('landing.simulator.unit_per_day') }}</span>
                        </div>
                    </div>

                    <!-- Input 3: Days to Expiry -->
                    <div>
                        <label for="simDays" class="block text-xs font-semibold text-[#0B1F4B] mb-2">
                            {{ __('landing.simulator.expiry_label') }}
                        </label>
                        <div class="relative">
                            <input type="number" id="simDays" value="45" min="1" max="730" oninput="calculateSim()"
                                class="w-full px-3 py-2 border border-[#DBE5F5] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1D4ED8] focus:outline-none">
                            <span class="absolute right-3 top-2 text-xs text-[#52627F]">{{ __('landing.simulator.days') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Simulation Output Matrix -->
                <div class="bg-slate-50 border border-[#DBE5F5] rounded-xl p-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#52627F] mb-4">{{ __('landing.simulator.results_title') }}
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="p-3 bg-white rounded-lg border border-[#DBE5F5]">
                            <span class="block text-[11px] text-[#52627F]">{{ __('landing.simulator.days_to_deplete') }}</span>
                            <span id="outDeplete" class="text-lg font-mono font-bold text-[#0B1F4B]">{{ __('landing.simulator.initial_days') }}</span>
                        </div>
                        <div class="p-3 bg-white rounded-lg border border-[#DBE5F5]">
                            <span class="block text-[11px] text-[#52627F]">{{ __('landing.simulator.projected_consumption') }}</span>
                            <span id="outConsumed" class="text-lg font-mono font-bold text-[#0B1F4B]">{{ __('landing.simulator.initial_consumed') }}</span>
                        </div>
                        <div class="p-3 bg-white rounded-lg border border-[#DBE5F5]">
                            <span class="block text-[11px] text-[#52627F]">{{ __('landing.simulator.projected_waste') }}</span>
                            <span id="outWaste" class="text-lg font-mono font-bold text-[#B33A0A]">{{ __('landing.simulator.initial_waste') }}</span>
                        </div>
                        <div class="p-3 bg-white rounded-lg border border-[#DBE5F5]">
                            <span class="block text-[11px] text-[#52627F]">{{ __('landing.simulator.waste_ratio') }}</span>
                            <span id="outRatio" class="text-lg font-mono font-bold text-[#B33A0A]">62.5%</span>
                        </div>
                    </div>

                    <!-- Risk Evaluation Outcome -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white rounded-lg border border-[#DBE5F5] gap-4">
                        <div>
                            <span class="text-xs text-[#52627F] block">{{ __('landing.simulator.risk_classification') }}</span>
                            <span id="outRiskBadge"
                                class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-orange-100 text-[#B33A0A] border border-orange-300">
                                {{ __('landing.simulator.initial_badge') }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-[#52627F] block">{{ __('landing.simulator.recommendation') }}</span>
                            <span id="outAction" class="text-xs font-bold text-[#1D4ED8]">
                                {{ __('landing.simulator.initial_action') }}
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
                <span class="text-xs font-bold text-[#1D4ED8] uppercase tracking-wider">{{ __('landing.principles.eyebrow') }}</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0B1F4B] mt-2 mb-4">
                    {{ __('landing.principles.title') }}
                </h2>
                <p class="text-sm text-[#52627F]">
                    {{ __('landing.principles.subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Principle 1 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div
                        class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        01
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">{{ __('landing.principles.one_title') }}</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        {{ __('landing.principles.one_body') }}
                    </p>
                </div>

                <!-- Principle 2 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div
                        class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        02
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">{{ __('landing.principles.two_title') }}</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        {{ __('landing.principles.two_body') }}
                    </p>
                </div>

                <!-- Principle 3 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div
                        class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        03
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">{{ __('landing.principles.three_title') }}</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        {{ __('landing.principles.three_body') }}
                    </p>
                </div>

                <!-- Principle 4 -->
                <div class="p-6 rounded-xl border border-[#DBE5F5] bg-slate-50/50">
                    <div
                        class="w-10 h-10 rounded-lg bg-[#EAF1FF] text-[#1D4ED8] flex items-center justify-center font-bold mb-4">
                        04
                    </div>
                    <h3 class="text-base font-bold text-[#0B1F4B] mb-2">{{ __('landing.principles.four_title') }}</h3>
                    <p class="text-xs text-[#52627F] leading-relaxed">
                        {{ __('landing.principles.four_body') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Demo Portal -->
    <section class="py-16 bg-[#0B1F4B] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-4">
                {{ __('landing.cta.title') }}
            </h2>
            <p class="text-sm text-blue-200/90 max-w-2xl mx-auto mb-8">
                {{ __('landing.cta.body') }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('login') }}"
                    class="px-6 py-3 rounded-lg bg-[#1D4ED8] hover:bg-blue-600 font-bold text-sm text-white shadow-lg transition">
                    {{ __('landing.cta.sign_in') }}
                </a>
                <a href="{{ route('facility-requests.create') }}"
                    class="px-6 py-3 rounded-lg bg-white/10 hover:bg-white/20 font-semibold text-sm text-white border border-white/20 transition">
                    {{ __('landing.cta.request_facility') }}
                </a>
            </div>
        </div>
    </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#DBE5F5] py-6 text-center text-xs text-[#52627F]">
        <p><strong>MediLoop</strong> — {{ __('landing.footer.competition') }}</p>
        <p class="mt-1 font-mono text-[11px]">{{ __('landing.footer.standards') }}</p>
    </footer>

    <!-- Interactive Simulator Script (Vanilla JS) -->
    <script>
        const simStrings = @json(__('landing.simulator.js'));
        const simLocale = @json(str_replace('_', '-', app()->getLocale()) === 'id' ? 'id-ID' : 'en-US');
        const simDecimal = new Intl.NumberFormat(simLocale, {
            minimumFractionDigits: 1,
            maximumFractionDigits: 1,
        });
        const simInteger = new Intl.NumberFormat(simLocale, {
            maximumFractionDigits: 0,
        });

        function setPreset(stock, burn, days) {
            document.getElementById('simStock').value = stock;
            document.getElementById('simBurnRate').value = burn;
            document.getElementById('simDays').value = days;
            calculateSim();
        }

        function onMedicineChange(val) {
            if (val === 'ceftriaxone') setPreset(480, 4.0, 45);
            else if (val === 'insulin') setPreset(120, 1.0, 28);
            else if (val === 'amoxicillin') setPreset(300, 8.0, 80);
            else if (val === 'paracetamol') setPreset(300, 8.0, 80);
            else setPreset(480, 4.0, 45);
        }

        function calculateSim() {
            const stock = Math.max(0, parseFloat(document.getElementById('simStock').value) || 0);
            const burn = Math.max(0, parseFloat(document.getElementById('simBurnRate').value) || 0);
            const days = Math.max(0, parseFloat(document.getElementById('simDays').value) || 0);

            const daysToDeplete = burn > 0 ? simDecimal.format(stock / burn) : simStrings.infinite;
            const expectedConsumption = Math.floor(burn * days);
            const projectedWaste = Math.max(0, stock - expectedConsumption);
            const wasteRatio = stock > 0 ? ((projectedWaste / stock) * 100).toFixed(1) : '0.0';

            document.getElementById('outDeplete').textContent = burn > 0
                ? simStrings.days_suffix.replace(':value', daysToDeplete)
                : daysToDeplete;
            document.getElementById('outConsumed').textContent = simStrings.unit_suffix.replace(':value', simInteger.format(expectedConsumption));
            document.getElementById('outWaste').textContent = simStrings.unit_suffix.replace(':value', simInteger.format(projectedWaste));
            document.getElementById('outRatio').textContent = simDecimal.format(wasteRatio) + '%';

            const badge = document.getElementById('outRiskBadge');
            const action = document.getElementById('outAction');

            if (projectedWaste === 0) {
                badge.className = 'inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-slate-100 text-slate-700 border border-slate-300';
                badge.textContent = simStrings.low_badge;
                action.textContent = simStrings.low_action;
                action.className = 'text-xs font-bold text-slate-700';
            } else if (days <= 30) {
                badge.className = 'inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-red-100 text-[#B33A0A] border border-red-300';
                badge.textContent = simStrings.critical_badge.replace(':count', simInteger.format(projectedWaste));
                action.textContent = simStrings.critical_action;
                action.className = 'text-xs font-bold text-[#B33A0A]';
            } else if (days <= 60 || parseFloat(wasteRatio) >= 25) {
                badge.className = 'inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-orange-100 text-[#B33A0A] border border-orange-300';
                badge.textContent = simStrings.high_badge
                    .replace(':count', simInteger.format(projectedWaste))
                    .replace(':ratio', simDecimal.format(wasteRatio));
                action.textContent = simStrings.high_action;
                action.className = 'text-xs font-bold text-[#1D4ED8]';
            } else {
                badge.className = 'inline-block mt-1 px-3 py-1 text-xs font-bold rounded bg-amber-100 text-[#B7791F] border border-amber-300';
                badge.textContent = simStrings.medium_badge.replace(':count', simInteger.format(projectedWaste));
                action.textContent = simStrings.medium_action;
                action.className = 'text-xs font-bold text-[#B7791F]';
            }
        }

        calculateSim();
    </script>
</body>

</html>