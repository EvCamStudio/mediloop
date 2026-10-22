@extends('layouts.mediloop', ['title' => 'Inventaris & Data Stok Obat — MediLoop'])

@section('content')
<div class="ml-dashboard">

    {{-- Flash Alerts --}}
    @if(session('status'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: #F0FDF4; border: 1px solid #86EFAC; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #166534; font-size: 13px; font-weight: 600; box-shadow: 0 1px 3px rgba(34, 197, 94, 0.08);">
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
        <div style="margin-bottom: 20px; padding: 14px 18px; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 12px; display: flex; align-items: center; gap: 12px; color: #991B1B; font-size: 13px; font-weight: 600; box-shadow: 0 1px 3px rgba(239, 68, 68, 0.08);">
            <svg style="width: 20px; height: 20px; color: #DC2626; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ $errors->first('delete_error') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <header class="ml-page-head" style="margin-bottom: 24px;">
        <div>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Inventaris &amp; Data Stok Batch Obat</h1>
                <span class="ml-facility-chip">{{ $facility?->name ?? 'Fasilitas Terhubung' }}</span>
            </div>
            <p class="ml-page-sub">
                Kelola data stok aktif, pantau risiko limbah kedaluwarsa, dan identifikasi peluang redistribusi antarfaskes.
            </p>
        </div>

        <div class="ml-page-actions">
            @if(! $user->isDinkesAuditor())
                <a href="{{ route('batches.create') }}" class="ml-btn-primary" style="padding: 9px 18px; font-size: 13px;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Input Stok Obat Baru</span>
                </a>
            @endif
        </div>
    </header>

    {{-- Summary Stat Cards (Airy & Spacious) --}}
    <div class="ml-stat-grid" style="margin-bottom: 24px;">
        {{-- Total Batches --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Total Batch Terdata</div>
            <div class="ml-stat-value" style="font-size: 24px; font-weight: 800; color: var(--ml-text); margin: 6px 0 4px; font-family: var(--font-heading);">
                {{ number_format($totalBatchesCount ?? $batches->total()) }}
            </div>
            <div class="ml-stat-sub">
                <span>{{ $batches->total() }} batch pada filter ini</span>
            </div>
        </div>

        {{-- Total Stock Units --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Total Stok Fisik</div>
            <div class="ml-stat-value" style="font-size: 24px; font-weight: 800; color: var(--ml-primary); margin: 6px 0 4px; font-family: var(--font-heading);">
                {{ number_format($totalStockUnits ?? 0) }}
            </div>
            <div class="ml-stat-sub">
                <span>Unit sediaan di inventaris</span>
            </div>
        </div>

        {{-- At-Risk Batches --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Batch Berisiko Kedaluwarsa</div>
            <div style="margin: 6px 0 4px;">
                <span class="ml-stat-badge" style="font-size: 16px; padding: 3px 12px;">
                    {{ number_format($atRiskCount ?? 0) }} Batch
                </span>
            </div>
            <div class="ml-stat-sub">
                <b>Status Kritis &amp; Tinggi</b>
            </div>
        </div>

        {{-- Projected Waste Value --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Potensi Nilai Limbah</div>
            <div style="margin: 6px 0 4px;">
                <span class="ml-stat-badge-blue" style="font-size: 16px; padding: 3px 12px; background: #0284C7;">
                    Rp {{ number_format($totalWasteValue ?? 0, 0, ',', '.') }}
                </span>
            </div>
            <div class="ml-stat-sub">
                <span>Dapat diselamatkan via alokasi</span>
            </div>
        </div>
    </div>

    {{-- Spacious Filter & Search Bar --}}
    <div class="ml-section" style="padding: 18px 22px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('batches.index') }}" style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                {{-- Facility filter (for Dinkes / Sysadmin) --}}
                @if(($user->isDinkesAuditor() || $user->role === 'SYSADMIN') && isset($facilitiesList))
                    <div style="min-width: 220px; flex: 1;">
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
                <div style="flex: 2; min-width: 260px; position: relative;">
                    <label for="filter-search" class="ml-sr-only">Pencarian Obat</label>
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--ml-subtle); pointer-events: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="filter-search" name="search" value="{{ $currentSearch }}" placeholder="Cari nama obat, nomor batch, bentuk sediaan, atau lokasi rak..." style="width: 100%; height: 42px; padding: 0 14px 0 40px; background: var(--ml-surface); border: 1px solid var(--ml-border); border-radius: 10px; font-size: 13px; color: var(--ml-text); font-family: var(--font-body); outline: none; box-sizing: border-box; transition: border-color 0.15s, box-shadow 0.15s;">
                </div>

                {{-- Status Filter --}}
                <div style="min-width: 160px; flex: 1;">
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
                    <button type="submit" class="ml-btn-primary" style="height: 42px; padding: 0 20px; font-size: 13px; white-space: nowrap;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Terapkan</span>
                    </button>

                    @if($currentSearch || $currentStatus || request('facility_id'))
                        <a href="{{ route('batches.index') }}" class="ml-btn-quiet" style="height: 42px; padding: 0 16px; font-size: 13px;" title="Reset filter pencarian">
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Quick Filter Chips --}}
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; padding-top: 4px; border-top: 1px dashed var(--ml-border); font-size: 12px; color: var(--ml-muted);">
                <span style="font-weight: 600; color: var(--ml-text);">Filter Cepat:</span>
                <a href="{{ route('batches.index') }}" style="text-decoration: none; padding: 3px 10px; border-radius: 99px; border: 1px solid {{ empty($currentStatus) ? 'var(--ml-primary)' : 'var(--ml-border)' }}; background: {{ empty($currentStatus) ? '#EFF6FF' : '#fff' }}; color: {{ empty($currentStatus) ? 'var(--ml-primary)' : 'var(--ml-muted)' }}; font-weight: {{ empty($currentStatus) ? '700' : '500' }};">
                    Semua Status
                </a>
                <a href="{{ route('batches.index', array_merge(request()->except('status'), ['status' => 'ACTIVE'])) }}" style="text-decoration: none; padding: 3px 10px; border-radius: 99px; border: 1px solid {{ $currentStatus === 'ACTIVE' ? 'var(--ml-primary)' : 'var(--ml-border)' }}; background: {{ $currentStatus === 'ACTIVE' ? '#EFF6FF' : '#fff' }}; color: {{ $currentStatus === 'ACTIVE' ? 'var(--ml-primary)' : 'var(--ml-muted)' }}; font-weight: {{ $currentStatus === 'ACTIVE' ? '700' : '500' }};">
                    Hanya Aktif
                </a>
                <a href="{{ route('batches.index', array_merge(request()->except('status'), ['status' => 'RESERVED'])) }}" style="text-decoration: none; padding: 3px 10px; border-radius: 99px; border: 1px solid {{ $currentStatus === 'RESERVED' ? 'var(--ml-primary)' : 'var(--ml-border)' }}; background: {{ $currentStatus === 'RESERVED' ? '#EFF6FF' : '#fff' }}; color: {{ $currentStatus === 'RESERVED' ? 'var(--ml-primary)' : 'var(--ml-muted)' }}; font-weight: {{ $currentStatus === 'RESERVED' ? '700' : '500' }};">
                    Dicadangkan (Transfer)
                </a>
                <a href="{{ route('batches.index', array_merge(request()->except('status'), ['status' => 'EXPIRED'])) }}" style="text-decoration: none; padding: 3px 10px; border-radius: 99px; border: 1px solid {{ $currentStatus === 'EXPIRED' ? 'var(--ml-primary)' : 'var(--ml-border)' }}; background: {{ $currentStatus === 'EXPIRED' ? '#EFF6FF' : '#fff' }}; color: {{ $currentStatus === 'EXPIRED' ? 'var(--ml-primary)' : 'var(--ml-muted)' }}; font-weight: {{ $currentStatus === 'EXPIRED' ? '700' : '500' }};">
                    Kedaluwarsa
                </a>
            </div>
        </form>
    </div>

    {{-- Spacious Inventory Table Card --}}
    <div class="ml-section">
        {{-- Section Header with Legend --}}
        <div class="ml-section-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; padding: 20px 24px;">
            <div>
                <h2 class="ml-section-title">Daftar Batch Farmasi Faskes</h2>
                <p class="ml-section-sub">
                    Menampilkan {{ $batches->total() }} data batch terdata. Baris disusun berdasarkan kedaluwarsa terdekat.
                </p>
            </div>

            {{-- Legend Chips --}}
            <div style="display: flex; align-items: center; gap: 14px; font-size: 11.5px; color: var(--ml-muted); flex-wrap: wrap;">
                <span style="font-weight: 600; color: var(--ml-text);">Tingkat Risiko:</span>
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
                    <span>Sedang</span>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span>
                    <span>Rendah</span>
                </span>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="ml-table-responsive">
            <table class="ml-table" style="min-width: 920px;">
                <caption class="ml-sr-only">
                    Tabel data inventaris batch obat faskes dengan indikator risiko kedaluwarsa dan aksi alokasi.
                </caption>
                <thead>
                    <tr>
                        <th scope="col" style="width: 28%; padding-left: 24px;">Nama Obat &amp; Spesifikasi</th>
                        <th scope="col" style="width: 16%;">No. Batch &amp; Lokasi</th>
                        <th scope="col" class="is-right" style="width: 14%;">Stok Fisik &amp; Laju</th>
                        <th scope="col" style="width: 22%;">Masa Kedaluwarsa &amp; Runway</th>
                        <th scope="col" class="is-right" style="width: 12%;">Proyeksi Limbah</th>
                        <th scope="col" class="is-right" style="width: 8%; padding-right: 24px;">Aksi</th>
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
                            $isLowRisk = $risk === 'LOW';
                        @endphp
                        <tr style="transition: background 0.15s ease;">
                            {{-- Medicine & Specification --}}
                            <td style="padding-left: 24px;">
                                <div class="ml-cell-title" style="font-size: 14px; font-weight: 700; color: #0B1F4B;">
                                    {{ $b->medicine->name }}
                                </div>
                                <div class="ml-cell-meta" style="margin-top: 4px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <span>{{ $b->medicine->dosage_form }}</span>
                                    <span style="color: var(--ml-subtle);">&bull;</span>
                                    <span>{{ $b->medicine->category }}</span>
                                    @if($b->medicine->requires_cold_chain)
                                        <span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 6px; background: #E0F2FE; color: #0369A1; font-size: 10.5px; font-weight: 700; border: 1px solid #BAE6FD;">
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
                                    <span style="display: inline-block; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11.5px; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #F1F5F9; border: 1px solid #CBD5E1; color: #0F172A; letter-spacing: -0.01em;">
                                        {{ $b->batch_number }}
                                    </span>
                                </div>
                                <div class="ml-cell-meta" style="margin-top: 5px; display: flex; align-items: center; gap: 4px;">
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
                                    <span style="font-family: ui-monospace, monospace; font-weight: 600;">~{{ $b->daily_burn_rate }}</span> unit/hari
                                </div>
                            </td>

                            {{-- Expiry & Visual Runway --}}
                            <td>
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;">
                                    <div style="font-size: 12.5px; font-weight: 600; color: #0B1F4B;">
                                        Exp: {{ $b->expiry_date->format('d M Y') }}
                                    </div>
                                    <span style="font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 6px; {{ $daysToExpiry <= 30 ? 'background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;' : ($daysToExpiry <= 60 ? 'background: #FFEDD5; color: #9A3412; border: 1px solid #FED7AA;' : 'background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;') }}">
                                        {{ $daysToExpiry }} hari lagi
                                    </span>
                                </div>

                                <div class="ml-runway-text" style="margin-top: 6px; font-size: 11.5px;">
                                    <span style="color: var(--ml-muted);">
                                        Stok habis: <strong style="color: var(--ml-text);">{{ $daysToDeplete === null ? 'Tidak terpakai' : number_format($daysToDeplete, 0).' hari' }}</strong>
                                    </span>
                                </div>

                                {{-- Runway Visual Progress Bar --}}
                                <div class="ml-runway" style="margin-top: 8px; height: 6px; background: #E2E8F0; border-radius: 99px; position: relative; max-width: 100%;" title="Progres konsumsi vs batas kedaluwarsa">
                                    <span class="ml-runway-fill" style="position: absolute; top: 0; bottom: 0; left: 0; border-radius: 99px; background: var(--ml-primary); opacity: 0.5; width: {{ $depletePercent }}%;"></span>
                                    <span class="ml-runway-mark" style="position: absolute; top: 0; bottom: 0; width: 3px; margin-left: -1.5px; background: {{ $daysToExpiry <= 60 ? '#EF4444' : '#64748B' }}; border-radius: 2px; left: {{ $expiryPercent }}%;"></span>
                                </div>
                            </td>

                            {{-- Projected Waste & Risk --}}
                            <td class="is-right" style="text-align: right;">
                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                    {{-- Risk Badge --}}
                                    @if($risk === 'CRITICAL')
                                        <span class="ml-risk ml-risk-critical">KRITIS</span>
                                    @elseif($risk === 'HIGH')
                                        <span class="ml-risk ml-risk-high">TINGGI</span>
                                    @elseif($risk === 'MEDIUM')
                                        <span class="ml-risk ml-risk-medium">SEDANG</span>
                                    @else
                                        <span class="ml-risk ml-risk-low">RENDAH</span>
                                    @endif

                                    {{-- Projected Waste --}}
                                    @if($projectedWaste > 0)
                                        <div style="font-family: ui-monospace, monospace; font-size: 12.5px; font-weight: 800; color: #DC2626; margin-top: 2px;">
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
                            <td class="is-right" style="text-align: right; padding-right: 24px; white-space: nowrap;">
                                <div class="ml-row-actions" style="gap: 8px;">
                                    @if($projectedWaste > 0 && $b->current_stock > 0 && ! $user->isDinkesAuditor())
                                        <a href="{{ route('transfers.propose', $b) }}" class="ml-btn-primary ml-btn-sm" style="font-size: 11.5px; padding: 5px 10px;" title="Cari rekomendasi redistribusi surplus">
                                            <span>Usulkan</span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                        </a>
                                    @endif

                                    @if(! $user->isDinkesAuditor())
                                        <a href="{{ route('batches.edit', $b) }}" class="ml-btn-quiet ml-btn-sm" style="font-size: 11.5px; padding: 5px 10px;" title="Edit data batch">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('batches.destroy', $b) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data batch {{ $b->batch_number }}? Tindakan ini tidak dapat dibatalkan.');" style="display: inline; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-btn-quiet ml-btn-sm" style="font-size: 11.5px; padding: 5px 9px; color: #DC2626; border-color: #FECACA; background: #FEF2F2;" title="Hapus batch obat">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 60px 24px; text-align: center;">
                                <div class="ml-empty-state">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" style="margin: 0 auto 12px; color: var(--ml-subtle);">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <div class="ml-empty-title" style="font-size: 15px; font-weight: 700; color: #0B1F4B; margin-bottom: 6px;">
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
            <div style="padding: 16px 24px; border-top: 1px solid var(--ml-border); background: #FAFBFD;">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
