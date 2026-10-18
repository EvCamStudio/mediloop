<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MediLoop — Clinical & Logistical Decision Support' }}</title>

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
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 antialiased">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white border-b border-[#DBE5F5] shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand Identity -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-lg bg-[#1D4ED8] flex items-center justify-center text-white font-black text-xl shadow-xs group-hover:bg-blue-800 transition">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xl font-bold tracking-tight text-[#0B1F4B]">MediLoop</span>
                            <span class="hidden sm:inline-block ml-2 px-2 py-0.5 text-[10px] font-semibold text-blue-800 bg-[#EAF1FF] rounded border border-[#DBE5F5]">Decision Support</span>
                        </div>
                    </a>

                    <!-- Main Navigation Links -->
                    @auth
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-[#EAF1FF] text-[#1D4ED8] font-semibold' : 'text-[#52627F] hover:bg-slate-100 hover:text-[#0B1F4B]' }}">
                            Stok & Risiko Kedaluwarsa
                        </a>
                        <a href="{{ route('transfers.index') }}" class="px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('transfers.*') ? 'bg-[#EAF1FF] text-[#1D4ED8] font-semibold' : 'text-[#52627F] hover:bg-slate-100 hover:text-[#0B1F4B]' }}">
                            Redistribusi Obat
                        </a>
                        <a href="{{ route('audit.index') }}" class="px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('audit.*') ? 'bg-[#EAF1FF] text-[#1D4ED8] font-semibold' : 'text-[#52627F] hover:bg-slate-100 hover:text-[#0B1F4B]' }}">
                            Audit Regional Dinkes
                        </a>
                    </nav>
                    @endauth
                </div>

                <!-- Right Side: User & Facility Context -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Active Facility Badge -->
                        @if(auth()->user()->facility)
                        <div class="hidden lg:flex flex-col text-right">
                            <span class="text-xs font-semibold text-[#0B1F4B]">{{ auth()->user()->facility->name }}</span>
                            <span class="text-[11px] text-[#52627F]">{{ auth()->user()->facility->code }} • {{ auth()->user()->facility->type }}</span>
                        </div>
                        @endif

                        <!-- Role Pill -->
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-[#EAF1FF] text-[#1D4ED8] border border-[#DBE5F5]">
                            {{ auth()->user()->job_title ?? auth()->user()->role }}
                        </span>

                        <!-- User Profile & Logout -->
                        <div class="flex items-center gap-2 pl-2 border-l border-[#DBE5F5]">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="p-2 text-[#52627F] hover:text-[#D9480F] hover:bg-red-50 rounded-md transition" title="Keluar">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('demo.switch', 'donor-staff') }}" class="px-4 py-2 text-xs font-semibold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-md shadow-xs transition">
                            Masuk Demo Portal
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    <!-- Global Alert Notifications -->
    @if(session('status'))
        <div class="bg-blue-50 border-b border-blue-200">
            <div class="max-w-7xl mx-auto py-2.5 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-[#0B1F4B]">
                    <svg class="w-5 h-5 text-[#1D4ED8] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-orange-50 border-b border-orange-200">
            <div class="max-w-7xl mx-auto py-2.5 px-4 sm:px-6 lg:px-8 flex items-center gap-2 text-sm text-[#D9480F]">
                <svg class="w-5 h-5 text-[#D9480F] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer with Regulatory Notice -->
    <footer class="bg-white border-t border-[#DBE5F5] mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:flex sm:justify-between sm:items-center text-xs text-[#52627F]">
            <p><strong>MediLoop</strong> — Platform Pendukung Keputusan Klinis & Logistik Redistribusi Obat Antarfaskes.</p>
            <p class="mt-2 sm:mt-0 font-mono text-[11px]">Zero Patient Data (PHI) • Human-Approved Invariant • UU PDP & Kemenkes Aligned</p>
        </div>
    </footer>

    <!-- Universal 1-Click Floating Demo Role Switcher Bar -->
    <aside aria-label="Demo Role Switcher" class="fixed bottom-4 right-4 z-50 bg-[#0B1F4B] text-white p-3 rounded-xl shadow-2xl border border-blue-400/30 max-w-sm sm:max-w-md backdrop-blur-md">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-orange-400 animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-[#EAF1FF]">Demo Role Switcher (1-Klik)</span>
            </div>
            <span class="text-[10px] text-blue-200">Uji Alur Sistem</span>
        </div>
        <p class="text-[11px] text-blue-200/80 mb-2.5 leading-snug">Ganti peran seketika untuk menguji persetujuan berjenjang dan otorisasi wewenang:</p>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 text-[11px]">
            <a href="{{ route('demo.switch', 'donor-staff') }}" class="px-2 py-1.5 rounded bg-blue-900/80 hover:bg-[#1D4ED8] transition text-center truncate {{ auth()->check() && auth()->user()->role === 'DONOR_STAFF' ? 'ring-2 ring-white font-bold bg-[#1D4ED8]' : '' }}" title="Staf Farmasi RSUD Tarakan">
                🏥 RSUD Staf
            </a>
            <a href="{{ route('demo.switch', 'donor-head') }}" class="px-2 py-1.5 rounded bg-blue-900/80 hover:bg-[#1D4ED8] transition text-center truncate {{ auth()->check() && auth()->user()->role === 'DONOR_APPROVER' ? 'ring-2 ring-white font-bold bg-[#1D4ED8]' : '' }}" title="Kepala Farmasi RSUD Tarakan (Approver)">
                🩺 RSUD Kepala
            </a>
            <a href="{{ route('demo.switch', 'recipient-staff') }}" class="px-2 py-1.5 rounded bg-blue-900/80 hover:bg-[#1D4ED8] transition text-center truncate {{ auth()->check() && auth()->user()->role === 'RECIPIENT_STAFF' ? 'ring-2 ring-white font-bold bg-[#1D4ED8]' : '' }}" title="Apoteker Puskesmas Gambir">
                🏥 PKM Staf
            </a>
            <a href="{{ route('demo.switch', 'recipient-head') }}" class="px-2 py-1.5 rounded bg-blue-900/80 hover:bg-[#1D4ED8] transition text-center truncate {{ auth()->check() && auth()->user()->role === 'RECIPIENT_APPROVER' ? 'ring-2 ring-white font-bold bg-[#1D4ED8]' : '' }}" title="Kepala Puskesmas Gambir (Penerima Fisik)">
                👨‍⚕️ PKM Kepala
            </a>
            <a href="{{ route('demo.switch', 'auditor') }}" class="col-span-2 sm:col-span-1 px-2 py-1.5 rounded bg-blue-900/80 hover:bg-[#1D4ED8] transition text-center truncate {{ auth()->check() && auth()->user()->role === 'DINKES_AUDITOR' ? 'ring-2 ring-white font-bold bg-[#1D4ED8]' : '' }}" title="Pengawas Farmalkes Dinkes DKI">
                🏛️ Dinkes Pengawas
            </a>
        </div>
    </aside>

</body>
</html>
