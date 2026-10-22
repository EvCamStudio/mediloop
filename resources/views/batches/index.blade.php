@extends('layouts.mediloop', ['title' => 'Inventaris & Prediksi Risiko Farmasi — MediLoop'])

@section('content')
<div class="ml-dashboard">

    {{-- Flash Alerts --}}
    @if(session('status'))
        <div style="margin-bottom: 22px; padding: 14px 20px; background: #F0FDF4; border: 1px solid #86EFAC; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 14px; color: #166534; font-size: 13px; font-weight: 600; box-shadow: 0 1px 3px rgba(34, 197, 94, 0.08);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg style="width: 20px; height: 20px; color: #16A34A; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="color: #166534; background: none; border: none; cursor: pointer; font-size: 18px; line-height: 1; padding: 0 4px;" aria-label="Tutup notifikasi">&times;</button>
        </div>
    @endif

    @if($errors->has('delete_error'))
        <div style="margin-bottom: 22px; padding: 14px 20px; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 12px; display: flex; align-items: center; gap: 12px; color: #991B1B; font-size: 13px; font-weight: 600; box-shadow: 0 1px 3px rgba(239, 68, 68, 0.08);">
            <svg style="width: 20px; height: 20px; color: #DC2626; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ $errors->first('delete_error') }}</span>
        </div>
    @endif

    {{-- Clinical Telemetry Header --}}
    <header class="ml-page-head" style="margin-bottom: 26px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 99px; background: #EFF6FF; border: 1px solid #BFDBFE; font-size: 11px; font-weight: 700; color: #1D4ED8; text-transform: uppercase; letter-spacing: 0.04em;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);"></span>
                    Mesin Prediksi Aktif &bull; Algoritma Tri-Faktor
                </span>
                <span class="ml-facility-chip" style="font-weight: 700;">
                    {{ $facility?->name ?? 'Fasilitas Terhubung' }}
                </span>
            </div>
            <h1 class="ml-page-title" style="font-size: 24px; font-weight: 800; color: #0B1F4B; letter-spacing: -0.02em;">
                Pusat Kendali Stok &amp; Prediksi Kedaluwarsa
            </h1>
            <p class="ml-page-sub" style="font-size: 13.5px; max-width: 680px; margin-top: 6px;">
                Sistem pendukung keputusan klinis untuk memantau sisa umur obat (*runway*), menghitung proyeksi limbah, serta merekomendasikan redistribusi antarfaskes terdekat sebelum obat kedaluwarsa.
            </p>
        </div>

        <div class="ml-page-actions" style="gap: 12px;">
            @if(! $user->isDinkesAuditor())
                <a href="{{ route('batches.create') }}" class="ml-btn-primary" style="padding: 10px 20px; font-size: 13px; font-weight: 700; box-shadow: 0 2px 6px rgba(29, 78, 216, 0.2);">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Input Stok Obat Baru</span>
                </a>
            @endif
        </div>
    </header>

    {{-- Clinical Telemetry Metrics (4 Spacious Cards) --}}
    <div class="ml-stat-grid" style="margin-bottom: 26px;">
        {{-- Total Active Stock --}}
        <div class="ml-stat-card" style="padding: 22px 24px;">
            <div class="ml-stat-label" style="display: flex; align-items: center; justify-content: space-between;">
                <span>Total Stok Terdaftar</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ml-subtle);"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #0B1F4B; margin: 8px 0 4px; font-family: var(--font-heading);">
                {{ number_format($totalStockUnits ?? 0) }} <span style="font-size: 14px; font-weight: 500; color: var(--ml-muted);">unit</span>
            </div>
            <div class="ml-stat-sub" style="font-size: 12px;">
                Tersebar dalam <strong>{{ $totalBatchesCount }} batch farmasi</strong>
            </div>
        </div>

        {{-- Urgent Risk Expiry Batches --}}
        <div class="ml-stat-card" style="padding: 22px 24px; border-left: 4px solid #DC2626;">
            <div class="ml-stat-label" style="display: flex; align-items: center; justify-content: space-between;">
                <span style="color: #991B1B; font-weight: 700;">Batch Berisiko Kedaluwarsa</span>
                <span style="font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; background: #FEE2E2; color: #991B1B;">PRIORITAS</span>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #DC2626; margin: 8px 0 4px; font-family: var(--font-heading);">
                {{ number_format($atRiskCount ?? 0) }} <span style="font-size: 14px; font-weight: 500; color: var(--ml-muted);">batch</span>
            </div>
            <div class="ml-stat-sub" style="font-size: 12px; color: #9A3412;">
                Status <strong>Kritis (&le; 30h)</strong> atau <strong>Tinggi (&le; 60h)</strong>
            </div>
        </div>

        {{-- Projected Waste Value Saved --}}
        <div class="ml-stat-card" style="padding: 22px 24px; border-left: 4px solid #0284C7;">
            <div class="ml-stat-label" style="display: flex; align-items: center; justify-content: space-between;">
                <span>Nilai Surplus Terdeteksi</span>
                <span style="font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; background: #E0F2FE; color: #0369A1;">POTENSI SELAMAT</span>
            </div>
            <div style="font-size: 22px; font-weight: 800; color: #0369A1; margin: 8px 0 4px; font-family: var(--font-heading);">
                Rp {{ number_format($totalWasteValue ?? 0, 0, ',', '.') }}
            </div>
            <div class="ml-stat-sub" style="font-size: 12px;">
                Sebanyak <strong>{{ number_format($totalWasteUnits ?? 0) }} unit</strong> terancam tak terserap
            </div>
        </div>

        {{-- SDG 12 & Environmental Footprint --}}
        <div class="ml-stat-card" style="padding: 22px 24px; border-left: 4px solid #16A34A;">
            <div class="ml-stat-label" style="display: flex; align-items: center; justify-content: space-between;">
                <span style="color: #166534; font-weight: 700;">Dampak Lingkungan (SDG 12)</span>
                <span style="font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; background: #DCFCE7; color: #166534;">ANTI LIMBAH B3</span>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #166534; margin: 8px 0 4px; font-family: var(--font-heading);">
                ~{{ $estimatedWasteKg }} <span style="font-size: 14px; font-weight: 500; color: var(--ml-muted);">kg</span>
            </div>
            <div class="ml-stat-sub" style="font-size: 12px;">
                Estimasi limbah B3 medis dicegah via redistribusi
            </div>
        </div>
    </div>

    {{-- Highlight Spotlight: Tri-Factor Candidate Recommendation --}}
    @if(isset($topRiskBatch) && count($topCandidates) > 0)
        <section class="ml-section" style="margin-bottom: 26px; border: 1.5px solid #BFDBFE; background: #F8FBFF;">
            <div style="padding: 20px 24px; border-bottom: 1px solid #DBE5F5; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; border: 1px solid #BFDBFE; display: flex; align-items: center; justify-content: center; color: #1D4ED8;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24M14.83 9.17l4.24-4.24M14.83 14.83l4.24 4.24M9.17 14.83l-4.24 4.24"/></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 99px; background: #DC2626; color: #fff;">
                                REKOMENDASI MATCH UTAMA
                            </span>
                            <span style="font-size: 12px; color: var(--ml-muted);">Batch #{{ $topRiskBatch->batch_number }}</span>
                        </div>
                        <h2 style="margin: 3px 0 0; font-size: 16px; font-weight: 800; color: #0B1F4B; font-family: var(--font-heading);">
                            {{ $topRiskBatch->medicine->name }} &mdash; Surplus {{ number_format($topRiskBatch->projected_waste) }} unit
                        </h2>
                    </div>
                </div>

                <div style="font-size: 12px; color: var(--ml-muted); text-align: right;">
                    Formula: <strong style="color: #0B1F4B;">Kebutuhan 50%</strong> &bull; <strong style="color: #0B1F4B;">Jarak 30%</strong> &bull; <strong style="color: #0B1F4B;">Logistik 20%</strong>
                </div>
            </div>

            <div style="padding: 20px 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                @foreach($topCandidates as $c)
                    <div style="background: #fff; border: 1px solid #DBE5F5; border-radius: 12px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; gap: 14px; box-shadow: 0 1px 3px rgba(11, 31, 75, 0.04);">
                        <div>
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 8px;">
                                <div>
                                    <div style="font-size: 14px; font-weight: 800; color: #0B1F4B;">
                                        {{ $c['facility']->name }}
                                    </div>
                                    <div style="font-size: 11.5px; color: var(--ml-muted); margin-top: 2px;">
                                        {{ $c['facility']->type }} &bull; {{ $c['facility']->city ?? 'DKI Jakarta' }}
                                    </div>
                                </div>
                                <span style="font-size: 12px; font-weight: 800; padding: 3px 8px; border-radius: 8px; background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE;">
                                    {{ $c['match_score'] }}% Match
                                </span>
                            </div>

                            <div style="display: flex; gap: 12px; font-size: 11.5px; color: var(--ml-muted); margin-top: 10px; padding: 8px 10px; background: #F8FAFC; border-radius: 8px;">
                                <div>
                                    <span style="color: var(--ml-subtle);">Jarak:</span> <strong>{{ $c['distance_km'] }} km</strong>
                                </div>
                                <div>&bull;</div>
                                <div>
                                    <span style="color: var(--ml-subtle);">Batas Alokasi:</span> <strong>{{ number_format($c['intake_cap']) }} unit</strong>
                                </div>
                            </div>
                        </div>

                        @if(! $user->isDinkesAuditor())
                            <a href="{{ route('transfers.propose', $topRiskBatch) }}" class="ml-btn-primary ml-btn-sm" style="width: 100%; justify-content: center; padding: 8px 12px; font-size: 12.5px; font-weight: 700;">
                                <span>Kirim Usulan Alokasi ({{ number_format($c['intake_cap']) }} unit)</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Spacious Filter & Search Toolbar --}}
    <div class="ml-section" style="padding: 20px 24px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('batches.index') }}" style="display: flex; flex-direction: column; gap: 16px;">
            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                {{-- Facility selector (for Dinkes / Sysadmin) --}}
                @if(($user->isDinkesAuditor() || $user->role === 'SYSADMIN') && isset($facilitiesList))
                    <div style="min-width: 240px; flex: 1;">
                        <label for="filter-facility" class="ml-sr-only">Pilih Fasilitas</label>
                        <select id="filter-facility" name="facility_id" onchange="this.form.submit()" style="width: 100%; height: 42px; padding: 0 14px; background: var(--ml-surface); border: 1px solid var(--ml-border); border-radius: 10px; font-size: 13px; color: var(--ml-text); font-family: var(--font-body); outline: none;">
                            <option value="">Semua Fasilitas Kesehatan</option>
                            @foreach($facilitiesList as $f)
                                <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>
                                    {{ $f->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Keyword Search --}}
                <div style="flex: 2; min-width: 280px; position: relative;">
                    <label for="filter-search" class="ml-sr-only">Pencarian Obat</label>
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--ml-subtle); pointer-events: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="filter-search" name="search" value="{{ $currentSearch }}" placeholder="Cari nama obat, nomor batch, bentuk sediaan, atau lokasi rak..." style="width: 100%; height: 42px; padding: 0 14px 0 42px; background: var(--ml-surface); border: 1px solid var(--ml-border); border-radius: 10px; font-size: 13px; color: var(--ml-text); font-family: var(--font-body); outline: none; box-sizing: border-box;">
                </div>

                {{-- Status Filter --}}
                <div style="min-width: 170px; flex: 1;">
                    <label for="filter-status" class="ml-sr-only">Status Batch</label>
                    <select id="filter-status" name="status" onchange="this.form.submit()" style="width: 100%; height: 42px; padding: 0 14px; background: var(--ml-surface); border: 1px solid var(--ml-border); border-radius: 10px; font-size: 13px; color: var(--ml-text); font-family: var(--font-body); outline: none;">
                        <option value="">Status: Semua</option>
                        <option value="ACTIVE" {{ $currentStatus === 'ACTIVE' ? 'selected' : '' }}>ACTIVE (Aktif)</option>
                        <option value="RESERVED" {{ $currentStatus === 'RESERVED' ? 'selected' : '' }}>RESERVED (Dicadangkan)</option>
                        <option value="DEPLETED" {{ $currentStatus === 'DEPLETED' ? 'selected' : '' }}>DEPLETED (Habis)</option>
                        <option value="EXPIRED" {{ $currentStatus === 'EXPIRED' ? 'selected' : '' }}>EXPIRED (Kedaluwarsa)</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div style="display: flex; gap: 8px; align-items: center;">
                    <button type="submit" class="ml-btn-primary" style="height: 42px; padding: 0 20px; font-size: 13px; font-weight: 700; white-space: nowrap;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Saring</span>
                    </button>

                    @if($currentSearch || $currentStatus || request('facility_id'))
                        <a href="{{ route('batches.index') }}" class="ml-btn-quiet" style="height: 42px; padding: 0 16px; font-size: 13px;" title="Reset filter">
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Quick Filter Chips --}}
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; padding-top: 10px; border-top: 1px dashed var(--ml-border); font-size: 12px; color: var(--ml-muted);">
                <span style="font-weight: 700; color: #0B1F4B;">Pintasan Filter:</span>
                <a href="{{ route('batches.index') }}" style="text-decoration: none; padding: 4px 12px; border-radius: 99px; border: 1px solid {{ empty($currentStatus) ? 'var(--ml-primary)' : 'var(--ml-border)' }}; background: {{ empty($currentStatus) ? '#EFF6FF' : '#fff' }}; color: {{ empty($currentStatus) ? 'var(--ml-primary)' : 'var(--ml-muted)' }}; font-weight: {{ empty($currentStatus) ? '700' : '500' }};">
                    Semua Data ({{ $totalBatchesCount }})
                </a>
                <a href="{{ route('batches.index', array_merge(request()->except('status'), ['status' => 'ACTIVE'])) }}" style="text-decoration: none; padding: 4px 12px; border-radius: 99px; border: 1px solid {{ $currentStatus === 'ACTIVE' ? 'var(--ml-primary)' : 'var(--ml-border)' }}; background: {{ $currentStatus === 'ACTIVE' ? '#EFF6FF' : '#fff' }}; color: {{ $currentStatus === 'ACTIVE' ? 'var(--ml-primary)' : 'var(--ml-muted)' }}; font-weight: {{ $currentStatus === 'ACTIVE' ? '700' : '500' }};">
                    Hanya Stok Aktif
                </a>
                <a href="{{ route('batches.index', array_merge(request()->except('status'), ['status' => 'RESERVED'])) }}" style="text-decoration: none; padding: 4px 12px; border-radius: 99px; border: 1px solid {{ $currentStatus === 'RESERVED' ? 'var(--ml-primary)' : 'var(--ml-border)' }}; background: {{ $currentStatus === 'RESERVED' ? '#EFF6FF' : '#fff' }}; color: {{ $currentStatus === 'RESERVED' ? 'var(--ml-primary)' : 'var(--ml-muted)' }}; font-weight: {{ $currentStatus === 'RESERVED' ? '700' : '500' }};">
                    Dicadangkan (Dalam Transfer)
                </a>
                <a href="{{ route('batches.index', array_merge(request()->except('status'), ['status' => 'EXPIRED'])) }}" style="text-decoration: none; padding: 4px 12px; border-radius: 99px; border: 1px solid {{ $currentStatus === 'EXPIRED' ? 'var(--ml-primary)' : 'var(--ml-border)' }}; background: {{ $currentStatus === 'EXPIRED' ? '#EFF6FF' : '#fff' }}; color: {{ $currentStatus === 'EXPIRED' ? 'var(--ml-primary)' : 'var(--ml-muted)' }}; font-weight: {{ $currentStatus === 'EXPIRED' ? '700' : '500' }};">
                    Kedaluwarsa
                </a>
            </div>
        </form>
    </div>

    {{-- Main Inventory Decision Table Card --}}
    <div class="ml-section">
        {{-- Section Header with Legend --}}
        <div class="ml-section-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; padding: 22px 26px;">
            <div>
                <h2 class="ml-section-title" style="font-size: 17px; font-weight: 800; color: #0B1F4B;">
                    Matriks Inventaris &amp; Analisis Runway Batch Farmasi
                </h2>
                <p class="ml-section-sub" style="font-size: 13px; margin-top: 4px;">
                    Menampilkan <strong>{{ $batches->total() }}</strong> batch. Klik tombol <em>Simulasi Laju</em> pada baris untuk menguji skenario perubahan konsumsi.
                </p>
            </div>

            {{-- Legend Guide --}}
            <div style="display: flex; align-items: center; gap: 14px; font-size: 11.5px; color: var(--ml-muted); flex-wrap: wrap;">
                <span style="font-weight: 700; color: #0B1F4B;">Klasifikasi Risiko:</span>
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #DC2626;"></span>
                    <span>Kritis (&le; 30h)</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #EA580C;"></span>
                    <span>Tinggi (31-60h)</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #D97706;"></span>
                    <span>Sedang (61-90h)</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span>
                    <span>Rendah (Aman)</span>
                </span>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="ml-table-responsive">
            <table class="ml-table" style="min-width: 960px;">
                <caption class="ml-sr-only">
                    Tabel matriks inventaris farmasi lengkap beserta analisis runway sisa masa pakai dan rekomendasi redistribusi.
                </caption>
                <thead>
                    <tr style="background: #FAFBFD;">
                        <th scope="col" style="width: 28%; padding-left: 26px;">Obat &amp; Spesifikasi Klinis</th>
                        <th scope="col" style="width: 16%;">No. Batch &amp; Lokasi Rak</th>
                        <th scope="col" class="is-right" style="width: 14%;">Stok Fisik &amp; Laju Pakai</th>
                        <th scope="col" style="width: 22%;">Kedaluwarsa &amp; Runway</th>
                        <th scope="col" class="is-right" style="width: 12%;">Proyeksi Limbah</th>
                        <th scope="col" class="is-right" style="width: 8%; padding-right: 26px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $b)
                        @php
                            $risk = $b->risk_level;
                            $daysToExpiry = (int) $b->days_to_expiry;
                            $daysToDeplete = is_finite($b->days_to_deplete) ? max(0, (float) $b->days_to_deplete) : null;
                            $projectedWaste = (int) $b->projected_waste;
                            $timelineDays = max($daysToExpiry, (int) ($daysToDeplete ?? $daysToExpiry), 1);
                            $depletePercent = $daysToDeplete === null ? 100 : min(100, round($daysToDeplete / $timelineDays * 100, 1));
                            $expiryPercent = min(100, round($daysToExpiry / $timelineDays * 100, 1));
                            $isUrgent = in_array($risk, ['CRITICAL', 'HIGH']);
                            $simId = 'sim-'.$b->id;
                        @endphp
                        <tr style="transition: background 0.15s ease; border-bottom: 1px solid var(--ml-border);">
                            {{-- Medicine & Specification --}}
                            <td style="padding-left: 26px;">
                                <div class="ml-cell-title" style="font-size: 14px; font-weight: 800; color: #0B1F4B; letter-spacing: -0.01em;">
                                    {{ $b->medicine->name }}
                                </div>
                                <div class="ml-cell-meta" style="margin-top: 5px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <span>{{ $b->medicine->dosage_form }}</span>
                                    <span style="color: var(--ml-subtle);">&bull;</span>
                                    <span>{{ $b->medicine->category }}</span>
                                    @if($b->medicine->requires_cold_chain)
                                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 6px; background: #E0F2FE; color: #0369A1; font-size: 10.5px; font-weight: 700; border: 1px solid #BAE6FD;">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                                <path d="M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9L4.9 19.1"/>
                                            </svg>
                                            <span>Rantai Dingin</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Batch & Location --}}
                            <td>
                                <div>
                                    <span style="display: inline-block; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11.5px; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: #F1F5F9; border: 1px solid #CBD5E1; color: #0F172A; letter-spacing: -0.01em;">
                                        {{ $b->batch_number }}
                                    </span>
                                </div>
                                <div class="ml-cell-meta" style="margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ml-subtle); flex-shrink: 0;" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span style="font-size: 11.5px;">{{ $b->storage_location ?? 'Gudang Farmasi Utama' }}</span>
                                </div>
                            </td>

                            {{-- Stock & Burn Rate --}}
                            <td class="ml-cell-stock" style="text-align: right;">
                                <div>
                                    <span class="ml-stock-value" style="font-size: 15px; font-weight: 800; color: #0B1F4B;">
                                        {{ number_format($b->current_stock) }}
                                    </span>
                                    <span class="ml-stock-unit" style="font-size: 12px; font-weight: 500; color: var(--ml-muted);">
                                        {{ $b->medicine->unit }}
                                    </span>
                                </div>
                                <div class="ml-cell-meta" style="margin-top: 4px; font-size: 11.5px; color: var(--ml-muted);">
                                    <span style="font-family: ui-monospace, monospace; font-weight: 700; color: #0B1F4B;">~{{ $b->daily_burn_rate }}</span> unit/hari
                                </div>
                            </td>

                            {{-- Expiry & Runway --}}
                            <td>
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;">
                                    <div style="font-size: 12.5px; font-weight: 700; color: #0B1F4B;">
                                        Exp: {{ $b->expiry_date->format('d M Y') }}
                                    </div>
                                    <span style="font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 6px; {{ $daysToExpiry <= 30 ? 'background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;' : ($daysToExpiry <= 60 ? 'background: #FFEDD5; color: #9A3412; border: 1px solid #FED7AA;' : 'background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;') }}">
                                        {{ $daysToExpiry }} hari lagi
                                    </span>
                                </div>

                                <div class="ml-runway-text" style="margin-top: 6px; font-size: 11.5px;">
                                    <span style="color: var(--ml-muted);">
                                        Stok habis: <strong style="color: #0B1F4B;">{{ $daysToDeplete === null ? 'Tidak terpakai' : number_format($daysToDeplete, 0).' hari' }}</strong>
                                    </span>
                                </div>

                                {{-- Runway Progress Bar --}}
                                <div class="ml-runway" style="margin-top: 8px; height: 6px; background: #E2E8F0; border-radius: 99px; position: relative; max-width: 100%;" title="Bilah konsumsi vs ambang kedaluwarsa">
                                    <span class="ml-runway-fill" style="position: absolute; top: 0; bottom: 0; left: 0; border-radius: 99px; background: var(--ml-primary); opacity: 0.55; width: {{ $depletePercent }}%;"></span>
                                    <span class="ml-runway-mark" style="position: absolute; top: 0; bottom: 0; width: 3px; margin-left: -1.5px; background: {{ $daysToExpiry <= 60 ? '#EF4444' : '#64748B' }}; border-radius: 2px; left: {{ $expiryPercent }}%;"></span>
                                </div>

                                {{-- Quick Simulator Toggle --}}
                                <div style="margin-top: 6px;">
                                    <button type="button" onclick="toggleSimulator('{{ $simId }}')" style="background: none; border: none; padding: 0; color: #1D4ED8; font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: underline;">
                                        &plus; Simulasi Laju Konsumsi
                                    </button>
                                </div>
                            </td>

                            {{-- Projected Waste & Risk --}}
                            <td class="is-right" style="text-align: right;">
                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                    @if($risk === 'CRITICAL')
                                        <span class="ml-risk ml-risk-critical">KRITIS</span>
                                    @elseif($risk === 'HIGH')
                                        <span class="ml-risk ml-risk-high">TINGGI</span>
                                    @elseif($risk === 'MEDIUM')
                                        <span class="ml-risk ml-risk-medium">SEDANG</span>
                                    @else
                                        <span class="ml-risk ml-risk-low">RENDAH</span>
                                    @endif

                                    @if($projectedWaste > 0)
                                        <div style="font-family: ui-monospace, monospace; font-size: 13px; font-weight: 800; color: #DC2626; margin-top: 2px;">
                                            {{ number_format($projectedWaste) }} unit
                                        </div>
                                        <div style="font-size: 11px; color: var(--ml-muted);">
                                            Rp {{ number_format($projectedWaste * (float) $b->unit_price_idr, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <div style="font-size: 11.5px; font-weight: 700; color: #16A34A; margin-top: 2px;">
                                            0 (Aman)
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="is-right" style="text-align: right; padding-right: 26px; white-space: nowrap;">
                                <div class="ml-row-actions" style="gap: 8px;">
                                    @if($projectedWaste > 0 && $b->current_stock > 0 && ! $user->isDinkesAuditor())
                                        <a href="{{ route('transfers.propose', $b) }}" class="ml-btn-primary ml-btn-sm" style="font-size: 12px; font-weight: 700; padding: 6px 12px;" title="Cari rekomendasi redistribusi surplus">
                                            <span>Usulkan</span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                        </a>
                                    @endif

                                    @if(! $user->isDinkesAuditor())
                                        <a href="{{ route('batches.edit', $b) }}" class="ml-btn-quiet ml-btn-sm" style="font-size: 11.5px; padding: 6px 10px;" title="Edit data batch">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('batches.destroy', $b) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data batch {{ $b->batch_number }}?');" style="display: inline; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-btn-quiet ml-btn-sm" style="font-size: 11.5px; padding: 6px 9px; color: #DC2626; border-color: #FECACA; background: #FEF2F2;" title="Hapus batch obat">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Interactive What-If Simulator Row --}}
                        <tr id="{{ $simId }}" style="display: none; background: #F8FAFC;">
                            <td colspan="6" style="padding: 16px 26px; border-bottom: 2px solid #E2E8F0;">
                                <div style="background: #fff; border: 1px solid #BFDBFE; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 6px rgba(11, 31, 75, 0.05);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 99px; background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE;">
                                                SIMULATOR KEPUTUSAN KLINIS
                                            </span>
                                            <span style="font-size: 13px; font-weight: 700; color: #0B1F4B;">
                                                Uji Skenario Konsumsi: {{ $b->medicine->name }}
                                            </span>
                                        </div>
                                        <button type="button" onclick="toggleSimulator('{{ $simId }}')" style="background: none; border: none; font-size: 16px; color: var(--ml-muted); cursor: pointer;">&times;</button>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 2fr 3fr; gap: 24px; align-items: center;">
                                        <div>
                                            <label style="display: block; font-size: 12px; font-weight: 700; color: #0B1F4B; margin-bottom: 6px;">
                                                Simulasi Laju Konsumsi Harian: <span id="label-{{ $simId }}" style="color: #1D4ED8; font-size: 14px; font-weight: 800;">{{ $b->daily_burn_rate }}</span> unit/hari
                                            </label>
                                            <input type="range" min="0.5" max="{{ max(20, (float) $b->daily_burn_rate * 3) }}" step="0.5" value="{{ $b->daily_burn_rate }}" oninput="runSimulation('{{ $simId }}', {{ $b->current_stock }}, {{ $daysToExpiry }}, {{ $b->unit_price_idr }}, this.value)" style="width: 100%; cursor: pointer;">
                                            <div style="display: flex; justify-content: space-between; font-size: 10.5px; color: var(--ml-muted); margin-top: 4px;">
                                                <span>0.5 unit/hari (Lambat)</span>
                                                <span>{{ $b->daily_burn_rate }} (Saat ini)</span>
                                                <span>{{ max(20, (float) $b->daily_burn_rate * 3) }} unit/hari (Tinggi)</span>
                                            </div>
                                        </div>

                                        <div style="display: flex; gap: 16px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 12px 16px;">
                                            <div style="flex: 1;">
                                                <div style="font-size: 11px; color: var(--ml-muted);">Proyeksi Limbah Baru:</div>
                                                <div id="waste-{{ $simId }}" style="font-size: 15px; font-weight: 800; color: {{ $projectedWaste > 0 ? '#DC2626' : '#16A34A' }}; font-family: ui-monospace, monospace;">
                                                    {{ number_format($projectedWaste) }} unit
                                                </div>
                                            </div>
                                            <div style="width: 1px; background: #E2E8F0;"></div>
                                            <div style="flex: 1;">
                                                <div style="font-size: 11px; color: var(--ml-muted);">Sisa Hari Habis Stok:</div>
                                                <div id="deplete-{{ $simId }}" style="font-size: 15px; font-weight: 800; color: #0B1F4B; font-family: ui-monospace, monospace;">
                                                    {{ is_finite($daysToDeplete) ? round($daysToDeplete).' hari' : '∞' }}
                                                </div>
                                            </div>
                                            <div style="width: 1px; background: #E2E8F0;"></div>
                                            <div style="flex: 1;">
                                                <div style="font-size: 11px; color: var(--ml-muted);">Status Risiko Baru:</div>
                                                <div id="risk-{{ $simId }}" style="font-size: 13px; font-weight: 800; color: {{ $isUrgent ? '#DC2626' : '#16A34A' }};">
                                                    {{ $risk }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 60px 26px; text-align: center;">
                                <div class="ml-empty-state">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" style="margin: 0 auto 12px; color: var(--ml-subtle);">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <div class="ml-empty-title" style="font-size: 15px; font-weight: 800; color: #0B1F4B; margin-bottom: 6px;">
                                        Tidak Ada Data Batch Obat Ditemukan
                                    </div>
                                    <p class="ml-empty-copy" style="font-size: 13px; color: var(--ml-muted); max-width: 440px; margin: 0 auto 18px;">
                                        @if($currentSearch || $currentStatus)
                                            Tidak ada batch yang cocok dengan kriteria pencarian "{{ $currentSearch }}" atau status "{{ $currentStatus }}". Coba ubah atau reset filter Anda.
                                        @else
                                            Belum ada batch obat yang terdaftar di fasilitas ini. Tambahkan data batch obat pertama Anda untuk mengaktifkan pemantauan risiko dan alokasi surplus.
                                        @endif
                                    </p>
                                    <div style="display: flex; gap: 10px; justify-content: center; align-items: center;">
                                        @if($currentSearch || $currentStatus)
                                            <a href="{{ route('batches.index') }}" class="ml-btn-quiet">
                                                Reset Filter Pencarian
                                            </a>
                                        @endif
                                        @if(! $user->isDinkesAuditor())
                                            <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                                                Input Stok Obat Baru
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($batches->hasPages())
            <div style="padding: 18px 26px; border-top: 1px solid var(--ml-border); background: #FAFBFD;">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Interactive Client-side Simulator Script --}}
<script>
    function toggleSimulator(id) {
        const row = document.getElementById(id);
        if (!row) return;
        row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    }

    function runSimulation(id, currentStock, daysToExpiry, unitPrice, newBurnRate) {
        const burnRate = parseFloat(newBurnRate);
        document.getElementById('label-' + id).innerText = burnRate.toFixed(1);

        // Calculate expected consumption & projected waste
        const expectedConsumption = Math.floor(burnRate * daysToExpiry);
        const projectedWaste = Math.max(0, currentStock - expectedConsumption);

        // Days to deplete
        const daysToDeplete = burnRate > 0 ? (currentStock / burnRate).toFixed(1) : '∞';

        // Waste ratio
        const wasteRatio = currentStock > 0 ? (projectedWaste / currentStock) : 0;

        // Risk Level
        let newRisk = 'LOW';
        let riskColor = '#16A34A';
        if (projectedWaste > 0) {
            if (daysToExpiry <= 30) {
                newRisk = 'CRITICAL';
                riskColor = '#DC2626';
            } else if (daysToExpiry <= 60 || wasteRatio >= 0.25) {
                newRisk = 'HIGH';
                riskColor = '#EA580C';
            } else if (daysToExpiry <= 90 || projectedWaste > 0) {
                newRisk = 'MEDIUM';
                riskColor = '#D97706';
            }
        }

        // Update DOM
        const wasteEl = document.getElementById('waste-' + id);
        wasteEl.innerText = projectedWaste.toLocaleString('id-ID') + ' unit';
        wasteEl.style.color = projectedWaste > 0 ? '#DC2626' : '#16A34A';

        const depleteEl = document.getElementById('deplete-' + id);
        depleteEl.innerText = daysToDeplete + (burnRate > 0 ? ' hari' : '');

        const riskEl = document.getElementById('risk-' + id);
        riskEl.innerText = newRisk;
        riskEl.style.color = riskColor;
    }
</script>
@endsection
