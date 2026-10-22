@extends('layouts.mediloop', ['title' => 'Overview Dashboard - MediLoop'])

@section('content')
<div>

    <!-- Header & Action Row -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--ml-border);">
        <div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <h1 style="font-size: 22px; font-weight: 800; color: var(--ml-text); margin: 0;">
                    Ringkasan Logistik &amp; Prediksi Kedaluwarsa
                </h1>
                <span style="display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 700; background: #EFF6FF; color: var(--ml-primary); border: 1px solid #BFDBFE;">
                    {{ $facility?->name ?? 'Fasilitas Kesehatan' }}
                </span>
            </div>
            <p style="font-size: 12.5px; color: var(--ml-muted); margin: 4px 0 0;">
                Sistem pendukung keputusan pemantauan stok farmasi dan redistribusi antarfaskes.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('batches.index') }}" class="ml-btn-ghost" style="padding: 8px 14px; font-size: 12px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                <span>Kelola Semua Stok</span>
            </a>
            @if(! $user->isDinkesAuditor())
                <a href="{{ route('batches.create') }}" class="ml-btn-primary" style="padding: 8px 16px; font-size: 12px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
                    <span>Input Stok Obat Baru</span>
                </a>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 4 STAT CARDS --}}
    {{-- ============================================================ --}}
    <div class="ml-stat-grid">

        {{-- At-Risk Batches --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">At-Risk Batches</div>
            <div style="margin-bottom: 8px;">
                <span class="ml-stat-badge">{{ $atRiskCount ?? 0 }} Batches</span>
            </div>
            @php
                $highRiskCount = isset($batches) ? $batches->filter(fn($b) => $b->risk_level === 'HIGH' || $b->risk_level === 'CRITICAL')->count() : 0;
                $watchRiskCount = ($atRiskCount ?? 0) - $highRiskCount;
            @endphp
            <div class="ml-stat-sub">
                <b>{{ $highRiskCount }} High Risk</b>
                &nbsp;·&nbsp;
                {{ $watchRiskCount > 0 ? $watchRiskCount : 0 }} Watch
            </div>
        </div>

        {{-- Redistributed Medicine --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Redistributed Medicine</div>
            <div class="ml-stat-sublabel">SDG 3 &amp; 12 impact</div>
            <div class="ml-stat-value">{{ number_format(isset($batches) ? $batches->sum('current_stock') : 0) }} Units</div>
            <div class="ml-stat-sub" style="color: var(--ml-subtle);">{{ isset($batches) ? $batches->count() : 0 }} batches total</div>
        </div>

        {{-- Prevented Waste Value --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Prevented Waste Value</div>
            <div style="margin-bottom: 8px;">
                <span class="ml-stat-badge-blue">Rp {{ number_format($totalProjectedWasteValue ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="ml-stat-sub" style="color: var(--ml-subtle);">Saved</div>
        </div>

        {{-- Active Network Partners --}}
        <div class="ml-stat-card">
            <div class="ml-stat-label">Active Network Partners</div>
            <div class="ml-stat-value" style="font-size: 26px; margin-top: 8px;">
                {{ isset($facilitiesList) ? $facilitiesList->count() : 0 }} Health Facilities
            </div>
        </div>
    </div>

    {{-- Quick Analytics & Map Callout Banner --}}
    <div style="background: linear-gradient(135deg, #0B1F4B 0%, #1D4ED8 100%); border-radius: 14px; padding: 14px 20px; color: #ffffff; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(29, 78, 216, 0.15);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px; shrink: 0;">
                📊
            </div>
            <div>
                <div style="font-weight: 700; font-size: 13.5px; letter-spacing: -0.01em;">Pusat Visualisasi &amp; Analitik Prediktif Obat</div>
                <div style="font-size: 11.5px; opacity: 0.85;">Lihat grafik distribusi risiko, horizon kedaluwarsa, dan estimasi limbah finansial.</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('map.index') }}" style="background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.25); padding: 6px 14px; border-radius: 8px; font-size: 11.5px; font-weight: 700; text-decoration: none; transition: all 0.15s;">
                🗺️ Buka Peta Logistik
            </a>
            <a href="{{ route('analytics.index') }}" style="background: #ffffff; color: #1D4ED8; padding: 6px 16px; border-radius: 8px; font-size: 11.5px; font-weight: 800; text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.15s;">
                Buka Analitik &amp; Grafik &rarr;
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TWO-COLUMN: Table + Map Panel --}}
    {{-- ============================================================ --}}
    <div class="ml-two-col">

        {{-- LEFT: Forecast Table --}}
        <div class="ml-section">
            <div class="ml-section-header" style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h2 class="ml-section-title">Prediksi Risiko Kedaluwarsa &amp; Rekomendasi Match</h2>
                    <p style="font-size: 11.5px; color: var(--ml-muted); margin: 2px 0 0;">
                        Dihitung otomatis: Hari hingga kedaluwarsa vs Laju pemakaian harian faskes.
                    </p>
                </div>
                @if(! $user->isDinkesAuditor())
                    <a href="{{ route('batches.create') }}" class="ml-btn-primary" style="font-size: 11.5px; padding: 5px 10px;">
                        Tambah Batch
                    </a>
                @endif
            </div>
            <div class="ml-table-responsive">
                <table class="ml-table">
                <thead>
                    <tr>
                        <th>Nama Obat &amp; Sediaan</th>
                        <th>No. Batch</th>
                        <th>Stok Fisik</th>
                        <th>Hari Kedaluwarsa vs Habis</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches ?? [] as $batch)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--ml-text); font-size: 13.5px;">
                                    {{ $batch->medicine->name }}
                                </div>
                                <div style="font-size: 11px; color: var(--ml-muted); margin-top: 2px;">
                                    {{ $batch->medicine->dosage_form }} • {{ $batch->medicine->category }}
                                    @if($batch->medicine->requires_cold_chain)
                                        <span style="display: inline-block; margin-left: 4px; padding: 1px 4px; font-size: 9px; font-weight: 700; background: #E0F2FE; color: #0369A1; border-radius: 4px;">Rantai Dingin</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-family: monospace; font-weight: 600; color: var(--ml-text); background: #F1F5F9; padding: 2px 6px; border-radius: 4px;">
                                    {{ $batch->batch_number }}
                                </span>
                                <div style="font-size: 10px; color: var(--ml-muted); margin-top: 2px;">
                                    Exp: {{ $batch->expiry_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <span style="color: var(--ml-muted); font-size: 12px;">
                                    Stok:<br>
                                    <span style="font-weight: 700; color: var(--ml-text); font-size: 13.5px;">
                                        {{ number_format($batch->current_stock) }} {{ $batch->medicine->unit }}
                                    </span>
                                </span>
                            </td>
                            <td>
                                <div class="ml-days-wrap">
                                    <span class="ml-days-pill ml-days-expiry">
                                        {{ $batch->days_to_expiry }} Hari<br>
                                        <span style="font-size: 9.5px; font-weight:400;">Kedaluwarsa</span>
                                    </span>
                                    <span class="ml-days-vs">vs</span>
                                    <span class="ml-days-pill ml-days-deplete">
                                        {{ is_finite($batch->days_to_deplete) ? $batch->days_to_deplete : '∞' }} Hari<br>
                                        <span style="font-size: 9.5px; font-weight:400;">Habis Stok</span>
                                    </span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    @php $risk = $batch->risk_level; @endphp
                                    @if($risk === 'CRITICAL')
                                        <span class="ml-risk-high" style="background:#FEE2E2; color:#991B1B; border-color:#FCA5A5;">CRITICAL</span>
                                    @elseif($risk === 'HIGH')
                                        <span class="ml-risk-high">HIGH</span>
                                    @elseif($risk === 'MEDIUM')
                                        <span class="ml-risk-medium">MEDIUM</span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; padding: 4px 8px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; border-radius: 8px; font-size: 11px; font-weight: 700;">LOW</span>
                                    @endif

                                    @if(! $user->isDinkesAuditor() && $batch->projected_waste > 0 && $batch->current_stock > 0)
                                        <a href="{{ route('transfers.propose', $batch) }}" class="ml-btn-primary">
                                            Find Match
                                        </a>
                                    @else
                                        <a href="{{ route('batches.edit', $batch) }}" class="ml-btn-ghost" style="padding: 5px 10px; font-size: 11.5px;">
                                            Detail
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 36px 20px; text-align: center; color: var(--ml-muted);">
                                <div style="max-width: 380px; margin: 0 auto;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 10px; color: var(--ml-subtle);">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <div style="font-weight: 700; color: var(--ml-text); font-size: 14px; margin-bottom: 4px;">
                                        Belum Ada Data Stok di Fasilitas Ini
                                    </div>
                                    <p style="font-size: 12px; line-height: 1.5; margin-bottom: 16px;">
                                        Data obat yang Anda input akan langsung dianalisis oleh mesin prediksi untuk mendeteksi risiko limbah kedaluwarsa.
                                    </p>
                                    @if(! $user->isDinkesAuditor())
                                        <a href="{{ route('batches.create') }}" class="ml-btn-primary">
                                            Input Stok Obat Sekarang
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        {{-- RIGHT: Map + Match Suggestions --}}
        <div style="display: flex; flex-direction: column; gap: 16px;">

            {{-- Network Map --}}
            <div class="ml-section">
                <div class="ml-section-header" style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 class="ml-section-title">Peta Jejaring Faskes</h3>
                        <span style="font-size: 11px; color: var(--ml-muted);">Ringkasan radius antarfaskes</span>
                    </div>
                    <a href="{{ route('map.index') }}" class="ml-btn-ghost" style="padding: 4px 10px; font-size: 11px; text-decoration: none; font-weight: 700; color: var(--ml-primary); border: 1px solid #BFDBFE; background: #EFF6FF; border-radius: 6px;">
                        Buka Peta Penuh &rarr;
                    </a>
                </div>
                <div class="ml-map-container">
                    {{-- Vector grid simulating healthcare redistribution network --}}
                    <svg width="100%" height="100%" viewBox="0 0 340 200" xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;">
                        <rect width="340" height="200" fill="#E8EDF5"/>
                        <line x1="0" y1="80" x2="340" y2="80" stroke="#D1DCF0" stroke-width="5"/>
                        <line x1="0" y1="130" x2="340" y2="130" stroke="#D1DCF0" stroke-width="4"/>
                        <line x1="80" y1="0" x2="80" y2="200" stroke="#D1DCF0" stroke-width="4"/>
                        <line x1="200" y1="0" x2="200" y2="200" stroke="#D1DCF0" stroke-width="3"/>
                        <line x1="260" y1="0" x2="260" y2="200" stroke="#D1DCF0" stroke-width="2"/>
                        <rect x="90" y="90" width="100" height="30" fill="#D8E5F2" rx="2"/>
                        <rect x="210" y="100" width="40" height="20" fill="#D8E5F2" rx="2"/>
                        <line x1="68" y1="68" x2="270" y2="140" stroke="#93C5FD" stroke-width="2" stroke-dasharray="6 4"/>
                        @if(isset($partnerFacilities) && count($partnerFacilities) > 0)
                            <text x="130" y="118" font-size="9" fill="#1D4ED8" font-weight="700" font-family="DM Sans, sans-serif">
                                {{ $partnerFacilities[0]['distance_km'] ?? '1.8' }} km
                            </text>
                        @endif
                    </svg>

                    {{-- Source facility pin (User's real facility) --}}
                    <div class="ml-map-pin" style="top: 40px; left: 24px;">
                        <div class="ml-pin-dot" style="background: var(--ml-orange);"></div>
                        <div class="ml-pin-label">
                            {{ $facility?->name ?? 'Fasilitas Donor' }}
                            <small>{{ $facility?->city ?? 'DKI Jakarta' }}</small>
                            @if(($atRiskCount ?? 0) > 0)
                                <span style="display:inline-block; margin-top:1px; background: var(--ml-orange); color:#fff; font-size:8px; font-weight:700; padding:1px 5px; border-radius:4px;">SURPLUS</span>
                            @endif
                        </div>
                    </div>

                    {{-- Destination facility pin (Real Nearest Partner) --}}
                    @if(isset($partnerFacilities) && count($partnerFacilities) > 0)
                        <div class="ml-map-pin" style="bottom: 30px; right: 16px;">
                            <div class="ml-pin-dot" style="background: var(--ml-primary);"></div>
                            <div class="ml-pin-label">
                                {{ $partnerFacilities[0]['facility']->name }}
                                <small>{{ $partnerFacilities[0]['distance_km'] }} km &bull; {{ $partnerFacilities[0]['facility']->type }}</small>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Partner Facilities List Summary --}}
                <div style="padding: 0 16px 12px;">
                    <div style="font-size: 11.5px; font-weight: 700; color: var(--ml-muted); text-transform: uppercase; margin-bottom: 6px;">
                        Faskes Terdekat dalam Jejaring:
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        @forelse(array_slice($partnerFacilities ?? [], 0, 3) as $p)
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12px; padding: 4px 0; border-bottom: 1px dashed var(--ml-border);">
                                <span style="font-weight: 600; color: var(--ml-text);">
                                    {{ $p['facility']->name }}
                                </span>
                                <span style="font-family: monospace; font-weight: 700; color: var(--ml-primary); font-size: 11px;">
                                    {{ $p['distance_km'] }} km
                                </span>
                            </div>
                        @empty
                            <div style="font-size: 11.5px; color: var(--ml-muted);">
                                Tidak ada data faskes terdekat.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Match Suggestions --}}
                <div style="padding: 10px 16px 4px; border-top: 1px solid var(--ml-border);">
                    <div style="font-size: 13.5px; font-weight: 700; color: var(--ml-text); font-family: var(--font-heading); padding-bottom: 6px;">
                        Rekomendasi Match Redistribusi
                    </div>
                    @if(isset($topRiskBatch))
                        <div style="font-size: 11px; color: var(--ml-muted); margin-bottom: 8px;">
                            Untuk batch berisiko: <strong>{{ $topRiskBatch->medicine->name }}</strong> (No. {{ $topRiskBatch->batch_number }})
                        </div>
                    @endif
                </div>

                <div class="ml-match-list">
                    @forelse($matchCandidates ?? [] as $index => $candidate)
                        <div class="ml-match-item">
                            <div class="ml-match-rank">{{ $index + 1 }}</div>
                            <div class="ml-match-info">
                                <div class="ml-match-name">{{ $candidate['facility']->name }}</div>
                                <div class="ml-match-meta">
                                    Score {{ $candidate['match_score'] }}% &bull; Jarak {{ $candidate['distance_km'] }} km &bull; Kapasitas {{ $candidate['recipient_intake_cap'] }} unit
                                </div>
                            </div>
                            @if(isset($topRiskBatch) && ! $user->isDinkesAuditor())
                                <a href="{{ route('transfers.propose', $topRiskBatch) }}" class="ml-btn-primary" style="font-size: 11px; padding: 5px 10px; white-space: nowrap;">
                                    Ajukan Transfer
                                </a>
                            @endif
                        </div>
                    @empty
                        <div style="padding: 14px 0; text-align: center; color: var(--ml-muted); font-size: 12px;">
                            @if(isset($topRiskBatch))
                                Belum ada kandidat faskes penerima yang memenuhi syarat cold chain / jarak.
                            @else
                                Belum ada batch berisiko kedaluwarsa. Tambahkan stok batch untuk mendapatkan rekomendasi transfer otomatis.
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


