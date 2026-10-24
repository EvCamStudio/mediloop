@extends('layouts.mediloop', ['title' => 'Pengajuan Fasilitas'])

@section('content')
@php
    $tabs = [
        ['label' => 'Semua', 'value' => '', 'count' => $counts['total']],
        ['label' => 'Menunggu', 'value' => 'PENDING', 'count' => $counts['pending']],
        ['label' => 'Disetujui', 'value' => 'APPROVED', 'count' => $counts['approved']],
        ['label' => 'Ditolak', 'value' => 'REJECTED', 'count' => $counts['rejected']],
    ];
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Administrasi</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Pengajuan fasilitas</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">Pengajuan fasilitas</h1>
            <p class="ml-page-sub">Verifikasi identitas, izin, dan lokasi sebelum membuat fasilitas aktif.</p>
        </div>
    </header>

    @if(session('status'))
        <div class="ml-alert ml-alert-success" role="status">{{ session('status') }}</div>
    @endif

    <nav class="ml-registry-tabs" aria-label="Filter status pengajuan">
        @foreach($tabs as $tab)
            <a
                href="{{ $tab['value'] === '' ? route('admin.facility-requests.index') : route('admin.facility-requests.index', ['status' => $tab['value']]) }}"
                class="ml-registry-tab {{ $status === $tab['value'] ? 'is-active' : '' }}"
                @if($status === $tab['value']) aria-current="page" @endif
            >
                {{ $tab['label'] }}
                <span class="ml-tab-count">{{ number_format($tab['count'], 0, ',', '.') }}</span>
            </a>
        @endforeach
    </nav>

    <section class="ml-registry-panel" aria-labelledby="requests-table-title">
        <h2 id="requests-table-title" class="ml-sr-only">Daftar pengajuan fasilitas</h2>

        @if($requests->isEmpty())
            <div class="ml-empty-state">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M9 12h6m-6 4h6M7 3h10a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V5a2 2 0 0 1 2-2z"/></svg>
                <p class="ml-empty-title">Tidak ada pengajuan</p>
                <p class="ml-empty-copy">Pengajuan baru dari fasilitas akan muncul di sini.</p>
                <a href="{{ route('admin.facility-requests.index') }}" class="ml-btn-quiet">Lihat semua</a>
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy ml-table-registry ml-table-requests">
                    <caption class="ml-sr-only">Daftar pengajuan fasilitas</caption>
                    <thead>
                        <tr>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Diajukan</th>
                            <th scope="col">Status</th>
                            <th scope="col"><span class="ml-sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $requestRecord)
                            <tr>
                                <td>
                                    <div class="ml-facility-cell">
                                        <span class="ml-facility-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($requestRecord->name, 0, 1)) }}</span>
                                        <span>
                                            <a href="{{ route('admin.facility-requests.show', $requestRecord) }}" class="ml-facility-name">{{ $requestRecord->name }}</a>
                                            <span class="ml-cell-meta">
                                                <span class="ml-cell-mono">{{ $requestRecord->code }}</span>
                                                <span class="ml-meta-dot">·</span>
                                                {{ \App\Enums\FacilityType::tryFrom($requestRecord->type)?->label() ?? $requestRecord->type }}
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td data-label="Lokasi">
                                    <div>{{ $requestRecord->city ?: '—' }}</div>
                                    <div class="ml-cell-meta">{{ $requestRecord->province ?: 'Wilayah belum diisi' }}</div>
                                </td>
                                <td data-label="Diajukan" class="ml-cell-meta">
                                    {{ $requestRecord->created_at->format('d M Y') }}
                                </td>
                                <td data-label="Status">
                                    @include('admin.partials.status', ['status' => $requestRecord->status, 'label' => $requestRecord->status_label])
                                </td>
                                <td class="is-right">
                                    <a href="{{ route('admin.facility-requests.show', $requestRecord) }}" class="ml-row-open">
                                        Review
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
                {{ $requests->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
