<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MediLoop — platform pendukung keputusan redistribusi obat antarfasilitas kesehatan.">

    <title>{{ filled($title ?? null) ? $title.' — MediLoop' : 'MediLoop' }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F5F8FE] font-sans text-[#0B1F4B] antialiased">
    <a href="#auth-form"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-[#1D4ED8] focus:shadow-lg">
        Lewati ke formulir
    </a>

    <div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">
        {{-- Brand panel --}}
        <aside class="relative hidden overflow-hidden bg-[#0B1F4B] px-12 py-14 text-white lg:flex lg:flex-col"
            aria-label="Tentang MediLoop">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/10"
                aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-16 h-80 w-80 rounded-full border border-white/10"
                aria-hidden="true"></div>

            <a href="{{ route('home') }}" class="relative z-10 inline-flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="8" width="18" height="8" rx="4" transform="rotate(-45 12 12)" />
                        <path d="M8.5 8.5l7 7" />
                    </svg>
                </span>
                <span class="text-lg font-extrabold tracking-tight">MediLoop</span>
            </a>

            <div class="relative z-10 my-auto max-w-md py-16">
                <span
                    class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-blue-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                    Decision Support Only
                </span>

                <h2 class="text-3xl font-extrabold leading-tight tracking-tight">
                    Cegah pemusnahan obat kedaluwarsa melalui koordinasi antarfaskes.
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-blue-100/80">
                    MediLoop mendeteksi surplus obat berisiko kedaluwarsa, mencocokkannya dengan kebutuhan faskes
                    terdekat, lalu menyerahkan keputusan akhir kepada petugas.
                </p>

                <ul class="mt-10 space-y-4 text-sm text-blue-50/90">
                    <li class="flex items-start gap-3">
                        <span
                            class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.4" aria-hidden="true">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </span>
                        Prediksi risiko kedaluwarsa berbasis laju pemakaian harian.
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.4" aria-hidden="true">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </span>
                        Rekomendasi penerima dengan skor kebutuhan, jarak, dan logistik.
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.4" aria-hidden="true">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </span>
                        Zero data pasien — hanya data batch, SKU, dan fasilitas.
                    </li>
                </ul>
            </div>

            <p class="relative z-10 font-mono text-[11px] uppercase tracking-wider text-blue-200/60">
                RSUD · Puskesmas · Klinik · Dinkes
            </p>
        </aside>

        {{-- Form panel --}}
        <main class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}"
                    class="mb-8 inline-flex items-center gap-2.5 text-[#0B1F4B] lg:hidden">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#EAF1FF] text-[#1D4ED8]">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.9" aria-hidden="true">
                            <rect x="3" y="8" width="18" height="8" rx="4" transform="rotate(-45 12 12)" />
                            <path d="M8.5 8.5l7 7" />
                        </svg>
                    </span>
                    <span class="text-base font-extrabold tracking-tight">MediLoop</span>
                </a>

                <div id="auth-form" class="rounded-2xl border border-[#DBE5F5] bg-white p-6 shadow-sm sm:p-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs leading-relaxed text-[#52627F]">
                    Sistem pendukung keputusan. Setiap redistribusi tetap memerlukan persetujuan petugas faskes.
                </p>
            </div>
        </main>
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>
</html>
