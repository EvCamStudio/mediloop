<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('auth_ui.layout.meta_description') }}">

    <title>{{ filled($title ?? null) ? $title.' — MediLoop' : 'MediLoop' }}</title>

    @include('partials.favicons')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/json" id="js-translations">@json(__('js'))</script>
</head>
<body class="min-h-screen bg-surface-canvas font-sans text-text antialiased">
    <a href="#auth-form"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-[#1D4ED8] focus:shadow-lg">
        {{ __('auth_ui.layout.skip_to_form') }}
    </a>

    <div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">
        {{-- Brand panel --}}
        <aside class="relative hidden overflow-hidden bg-brand-navy px-12 py-14 text-white lg:flex lg:flex-col"
            aria-label="{{ __('auth_ui.layout.brand_aria') }}">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/10"
                aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-16 h-80 w-80 rounded-full border border-white/10"
                aria-hidden="true"></div>

            <a href="{{ route('home') }}" class="relative z-10 inline-flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="MediLoop" class="h-11 w-11 object-contain" />
                <span class="text-xl font-bold tracking-tight text-white">MediLoop</span>
            </a>

            <div class="relative z-10 my-auto max-w-md py-16">
                <span
                    class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-blue-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-200"></span>
                    {{ __('auth_ui.layout.panel_badge') }}
                </span>

                <h2 class="text-3xl font-semibold leading-tight tracking-tight">
                    {{ __('auth_ui.layout.panel_title') }}
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-blue-100/80">
                    {{ __('auth_ui.layout.panel_body') }}
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
                        {{ __('auth_ui.layout.point_1') }}
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.4" aria-hidden="true">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </span>
                        {{ __('auth_ui.layout.point_2') }}
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.4" aria-hidden="true">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </span>
                        {{ __('auth_ui.layout.point_3') }}
                    </li>
                </ul>
            </div>

            <p class="relative z-10 font-mono text-[11px] uppercase tracking-wider text-blue-200/60">
                {{ __('auth_ui.layout.facilities_line') }}
            </p>
        </aside>

        {{-- Form panel --}}
        <main class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center gap-2.5 text-text lg:hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="MediLoop" class="h-9 w-9 object-contain" />
                        <span class="text-lg font-bold tracking-tight text-text">MediLoop</span>
                    </a>

                    <div class="ms-auto"></div>
                </div>

                <div id="auth-form" class="rounded-2xl border border-border bg-surface-raised p-6 shadow-panel sm:p-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs leading-relaxed text-text-muted">
                    {{ __('auth_ui.layout.footer_note') }}
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
