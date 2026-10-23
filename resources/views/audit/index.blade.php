@extends('layouts.mediloop', ['title' => 'Pengawasan & Audit Regional'])

@section('content')
@php
    $displayTimezone = 'Asia/Jakarta';
    $auditorFacilityName = $user->facility?->name ?? 'Dinas Kesehatan';
    $completedCount = $completedTransfers->count();
    $auditCount = $auditLogs->total();
    $sortedTransfers = $completedTransfers->sortByDesc('completed_at');
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Pengawasan &amp; audit</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">Pengawasan regional</p>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Pengawasan Logistik &amp; Audit Redistribusi</h1>
                <span class="ml-facility-chip">{{ $auditorFacilityName }}</span>
            </div>
            <p class="ml-page-sub">
                Telusuri transaksi redistribusi yang selesai dan jejak perubahan status inventaris lintas fasilitas.
                Halaman ini tidak memuat data pasien.
            </p>
        </div>
    </header>

    <div class="ml-kpi-grid">
        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Unit terselamatkan</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($totalUnitsSaved, 0, ',', '.') }} <span>unit</span></div>
            <div class="ml-kpi-meta">Dari {{ number_format($completedCount, 0, ',', '.') }} transfer selesai</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Nilai terselamatkan</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value ml-kpi-value-currency">Rp {{ number_format($totalValueSavedIdr, 0, ',', '.') }}</div>
            <div class="ml-kpi-meta">Berdasarkan harga satuan batch</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Faskes terhubung</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4M9 11h.01M15 11h.01M9 15h.01M15 15h.01" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($facilitiesCount, 0, ',', '.') }} <span>faskes</span></div>
            <div class="ml-kpi-meta">Di luar fasilitas Dinas Kesehatan</div>
        </article>

        <article class="ml-kpi">
            <div class="ml-kpi-top">
                <span class="ml-kpi-label">Batch aktif terpantau</span>
                <span class="ml-kpi-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9" />
                    </svg>
                </span>
            </div>
            <div class="ml-kpi-value">{{ number_format($totalActiveBatches, 0, ',', '.') }} <span>batch</span></div>
            <div class="ml-kpi-meta">Batch berstatus aktif saat ini</div>
        </article>
    </div>

    <section class="ml-section" aria-labelledby="completed-title">
        <div class="ml-section-header ml-section-header-split">
            <div>
                <h2 class="ml-section-title" id="completed-title">Rekap Transaksi Redistribusi Berhasil</h2>
                <p class="ml-section-sub">
                    Transfer selesai dengan mutasi stok yang sudah tercatat. Diurutkan dari waktu selesai terbaru.
                </p>
            </div>
            <span class="ml-audit-count">{{ number_format($completedCount, 0, ',', '.') }} transfer selesai</span>
        </div>

        @if($completedCount === 0)
            <div class="ml-empty-state">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h6m0 0l-3-3m3 3l-3 3M3 7v10a2 2 0 002 2h7" />
                </svg>
                <p class="ml-empty-title">Belum ada transfer selesai</p>
                <p class="ml-empty-copy">Rekap akan muncul setelah transfer dikonfirmasi selesai dan stok dimutasi.</p>
            </div>
        @else
            <div class="ml-table-responsive" tabindex="0" role="region" aria-label="Tabel rekap transfer selesai, dapat digeser horizontal untuk melihat semua kolom">
                <table class="ml-table ml-table-audit">
                    <caption class="ml-sr-only">
                        Rekap transfer redistribusi yang telah selesai, termasuk obat, rute fasilitas, jumlah, nilai, kurir, dan waktu penyelesaian.
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode transfer</th>
                            <th scope="col">Obat &amp; batch</th>
                            <th scope="col">Donor &rarr; penerima</th>
                            <th scope="col" class="is-right">Jumlah</th>
                            <th scope="col" class="is-right">Nilai (IDR)</th>
                            <th scope="col">Kurir &amp; resi</th>
                            <th scope="col" class="is-right">Waktu selesai (WIB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sortedTransfers as $transfer)
                            <tr>
                                <td>
                                    <a href="{{ route('transfers.show', $transfer) }}" class="ml-audit-link">
                                        {{ $transfer->transfer_code }}
                                    </a>
                                </td>
                                <td>
                                    <div class="ml-cell-title">{{ $transfer->batch->medicine->name }}</div>
                                    <div class="ml-cell-meta ml-cell-mono">Batch {{ $transfer->batch->batch_number }}</div>
                                </td>
                                <td>
                                    <div class="ml-cell-title">{{ $transfer->donorFacility->name }}</div>
                                    <div class="ml-cell-meta">
                                        &rarr; {{ $transfer->recipientFacility->name }} &middot; {{ $transfer->distance_km }} km
                                    </div>
                                </td>
                                <td class="ml-cell-stock">
                                    <span class="ml-audit-amount">{{ number_format($transfer->quantity, 0, ',', '.') }}</span>
                                    <span class="ml-stock-unit">{{ $transfer->batch->medicine->unit }}</span>
                                </td>
                                <td class="is-right">
                                    <span class="ml-audit-amount">
                                        Rp {{ number_format($transfer->quantity * (float) $transfer->batch->unit_price_idr, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $transfer->courier_type ?? 'Kurir khusus farmasi' }}</div>
                                    <div class="ml-cell-meta ml-cell-mono">Resi {{ $transfer->tracking_number ?? '-' }}</div>
                                </td>
                                <td class="is-right">
                                    <span class="ml-audit-amount">
                                        {{ $transfer->completed_at?->timezone($displayTimezone)->format('d M Y, H:i') ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="ml-section" aria-labelledby="audit-title">
        <div class="ml-section-header ml-section-header-split">
            <div>
                <h2 class="ml-section-title" id="audit-title">Jejak Audit Sistem</h2>
                <p class="ml-section-sub">
                    Catatan tindakan perubahan status dan mutasi inventaris, diurutkan dari yang terbaru.
                </p>
            </div>
            <span class="ml-audit-count">{{ number_format($auditCount, 0, ',', '.') }} entri</span>
        </div>

        @if($auditLogs->isEmpty())
            <div class="ml-empty-state">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="ml-empty-title">Belum ada rekaman log audit</p>
                <p class="ml-empty-copy">Setiap perubahan status atau mutasi inventaris akan tercatat di sini.</p>
            </div>
        @else
            <div class="ml-table-responsive" tabindex="0" role="region" aria-label="Tabel jejak audit sistem, dapat digeser horizontal untuk melihat semua kolom">
                <table class="ml-table ml-table-audit">
                    <caption class="ml-sr-only">
                        Jejak audit sistem berisi waktu, aktor, fasilitas terkait, kode aksi, dan deskripsi perubahan.
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col">Waktu (WIB)</th>
                            <th scope="col">Aktor pengguna</th>
                            <th scope="col">Faskes terkait</th>
                            <th scope="col">Aksi</th>
                            <th scope="col">Deskripsi perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditLogs as $log)
                            <tr>
                                <td>
                                    <span class="ml-audit-amount">
                                        {{ $log->created_at->timezone($displayTimezone)->format('d M Y, H:i:s') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="ml-cell-title">{{ $log->actor?->name ?? 'Sistem Otomatis' }}</div>
                                    <div class="ml-cell-meta">
                                        {{ $log->actor ? ($log->actor->job_title ?: 'Petugas') : 'Aktivitas sistem' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="ml-cell-title">{{ $log->facility?->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="ml-audit-action">{{ $log->action }}</span>
                                </td>
                                <td>
                                    <span class="ml-audit-description">{{ $log->description ?: 'Tidak ada deskripsi.' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($auditLogs->hasPages())
                <div class="ml-pagination-wrap">
                    {{ $auditLogs->links() }}
                </div>
            @endif
        @endif
    </section>
</div>
@endsection
