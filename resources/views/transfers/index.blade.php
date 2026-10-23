@extends('layouts.mediloop', ['title' => 'Daftar Redistribusi Obat'])

@section('content')
@php
    $tabs = [
        ['label' => 'Semua', 'value' => ''],
        ['label' => 'Menunggu donor', 'value' => 'PROPOSED'],
        ['label' => 'Menunggu penerima', 'value' => 'APPROVED'],
        ['label' => 'Dalam pengiriman', 'value' => 'IN_TRANSIT'],
        ['label' => 'Selesai', 'value' => 'COMPLETED'],
    ];

    $statusLabels = [
        'PROPOSED' => ['label' => 'Menunggu donor', 'tone' => 'pending'],
        'APPROVED' => ['label' => 'Menunggu penerima', 'tone' => 'active'],
        'ACCEPTED' => ['label' => 'Siap kirim', 'tone' => 'active'],
        'IN_TRANSIT' => ['label' => 'Dalam pengiriman', 'tone' => 'transit'],
        'COMPLETED' => ['label' => 'Selesai', 'tone' => 'inactive'],
        'CANCELLED' => ['label' => 'Dibatalkan', 'tone' => 'suspended'],
    ];
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Redistribusi</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">Koordinasi antarfasilitas</p>
            <h1 class="ml-page-title">Redistribusi &amp; hibah obat</h1>
            <p class="ml-page-sub">Lacak status pengajuan dari otorisasi donor hingga mutasi stok fisik.</p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('dashboard') }}" class="ml-btn-quiet">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                Usulkan dari stok
            </a>
        </div>
    </header>

    <nav class="ml-registry-tabs" aria-label="Filter status transfer">
        @foreach($tabs as $tab)
            <a
                href="{{ $tab['value'] === '' ? route('transfers.index') : route('transfers.index', ['status' => $tab['value']]) }}"
                class="ml-registry-tab {{ $currentStatus === $tab['value'] ? 'is-active' : '' }}"
                @if($currentStatus === $tab['value']) aria-current="page" @endif
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>

    <section class="ml-registry-panel" aria-labelledby="transfers-title">
        <h2 id="transfers-title" class="ml-sr-only">Daftar transfer obat</h2>

        @if($transfers->isEmpty())
            <div class="ml-empty-state">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path d="M4 7h11l4 4v6H4z" />
                    <path d="M4 7V5h8v2M8 13h6" />
                </svg>
                <p class="ml-empty-title">Belum ada transfer pada kategori ini</p>
                <p class="ml-empty-copy">Usulan transfer muncul setelah stok berisiko diajukan dari dashboard.</p>
                <a href="{{ route('dashboard') }}" class="ml-btn-quiet">Kembali ke dashboard</a>
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy ml-table-registry ml-table-transfers">
                    <caption class="ml-sr-only">Daftar transfer dan status alurnya</caption>
                    <thead>
                        <tr>
                            <th scope="col">Transfer</th>
                            <th scope="col">Obat &amp; jumlah</th>
                            <th scope="col">Rute fasilitas</th>
                            <th scope="col">Skor</th>
                            <th scope="col">Status</th>
                            <th scope="col"><span class="ml-sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            @php
                                $statusMeta = $statusLabels[$transfer->status] ?? ['label' => $transfer->status, 'tone' => 'inactive'];
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('transfers.show', $transfer) }}" class="ml-transfer-code">{{ $transfer->transfer_code }}</a>
                                    <div class="ml-cell-meta">{{ $transfer->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td data-label="Obat & jumlah">
                                    <div class="ml-cell-title">{{ $transfer->batch->medicine->name }}</div>
                                    <div class="ml-cell-meta">
                                        {{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}
                                        <span class="ml-meta-dot">·</span>
                                        Batch {{ $transfer->batch->batch_number }}
                                    </div>
                                </td>
                                <td data-label="Rute fasilitas">
                                    <div class="ml-route">
                                        <span class="ml-route-name">{{ $transfer->donorFacility->name }}</span>
                                        <span class="ml-route-arrow" aria-hidden="true">→</span>
                                        <span class="ml-route-name is-recipient">{{ $transfer->recipientFacility->name }}</span>
                                    </div>
                                    <div class="ml-cell-meta">{{ $transfer->distance_km }} km · jarak udara</div>
                                </td>
                                <td data-label="Skor">
                                    <span class="ml-score">{{ $transfer->match_score }}%</span>
                                </td>
                                <td data-label="Status">
                                    <span class="ml-status ml-status-{{ $statusMeta['tone'] }}">{{ $statusMeta['label'] }}</span>
                                </td>
                                <td class="is-right">
                                    <a href="{{ route('transfers.show', $transfer) }}" class="ml-row-open">
                                        Lacak
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M5 12h14M13 6l6 6-6 6" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="ml-pagination-wrap">
                {{ $transfers->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
