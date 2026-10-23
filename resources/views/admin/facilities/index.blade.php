@extends('layouts.mediloop', ['title' => 'Registry Fasilitas'])

@section('content')
@php
    $baseParams = array_filter([
        'search' => $search !== '' ? $search : null,
        'type' => $type !== '' ? $type : null,
    ]);
    $tabs = [
        ['label' => 'Semua', 'value' => '', 'count' => $counts['total']],
        ['label' => 'Menunggu', 'value' => 'PENDING', 'count' => $counts['pending']],
        ['label' => 'Aktif', 'value' => 'ACTIVE', 'count' => $counts['active']],
        ['label' => 'Ditangguhkan', 'value' => 'SUSPENDED', 'count' => $counts['suspended']],
        ['label' => 'Nonaktif', 'value' => 'INACTIVE', 'count' => $counts['inactive']],
    ];
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Administrasi</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Fasilitas</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">Registry fasilitas kesehatan</h1>
            <p class="ml-page-sub">Satu sumber data untuk faskes yang boleh beroperasi di MediLoop.</p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('admin.facilities.create') }}" class="ml-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Tambah fasilitas
            </a>
        </div>
    </header>

    <nav class="ml-registry-tabs" aria-label="Filter status fasilitas">
        @foreach($tabs as $tab)
            @php
                $tabParams = $tab['value'] === ''
                    ? $baseParams
                    : array_merge($baseParams, ['status' => $tab['value']]);
                $isActive = $status === $tab['value'];
            @endphp
            <a
                href="{{ route('admin.facilities.index', $tabParams) }}"
                class="ml-registry-tab {{ $isActive ? 'is-active' : '' }}"
                @if($isActive) aria-current="page" @endif
            >
                {{ $tab['label'] }}
                <span class="ml-tab-count">{{ number_format($tab['count'], 0, ',', '.') }}</span>
            </a>
        @endforeach
    </nav>

    <section class="ml-registry-panel" aria-labelledby="registry-table-title">
        <h2 id="registry-table-title" class="ml-sr-only">Daftar fasilitas kesehatan</h2>

        <form method="GET" action="{{ route('admin.facilities.index') }}" class="ml-registry-toolbar" aria-label="Cari dan filter fasilitas">
            @if($status !== '')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <div class="ml-registry-search">
                <label for="search" class="ml-sr-only">Cari fasilitas</label>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="M20 20l-3.5-3.5" />
                </svg>
                <input id="search" name="search" type="search" class="ml-input" value="{{ $search }}" placeholder="Cari nama, kode, atau kota">
            </div>

            <div class="ml-registry-filter">
                <label for="type" class="ml-sr-only">Jenis fasilitas</label>
                <select id="type" name="type" class="ml-select">
                    <option value="">Semua jenis</option>
                    @foreach(\App\Enums\FacilityType::cases() as $typeOption)
                        @if($typeOption !== \App\Enums\FacilityType::Dinkes)
                            <option value="{{ $typeOption->value }}" @selected($type === $typeOption->value)>{{ $typeOption->label() }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="ml-registry-toolbar-actions">
                <button type="submit" class="ml-btn-primary">Terapkan</button>
                @if($search !== '' || $status !== '' || $type !== '')
                    <a href="{{ route('admin.facilities.index') }}" class="ml-btn-quiet">Reset</a>
                @endif
            </div>
        </form>

        @if($facilities->isEmpty())
            <div class="ml-empty-state">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-5h6v5"/></svg>
                <p class="ml-empty-title">Belum ada fasilitas yang cocok</p>
                <p class="ml-empty-copy">Ubah filter atau tambahkan fasilitas baru ke registry.</p>
                <a href="{{ route('admin.facilities.index') }}" class="ml-btn-quiet">Hapus filter</a>
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy ml-table-registry">
                    <caption class="ml-sr-only">Daftar fasilitas kesehatan di MediLoop</caption>
                    <thead>
                        <tr>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Wilayah</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tim</th>
                            <th scope="col">Diperbarui</th>
                            <th scope="col"><span class="ml-sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facilities as $facility)
                            <tr>
                                <td>
                                    <div class="ml-facility-cell">
                                        <span class="ml-facility-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($facility->name, 0, 1)) }}</span>
                                        <span>
                                            <a href="{{ route('admin.facilities.show', $facility) }}" class="ml-facility-name">
                                                {{ $facility->name }}
                                            </a>
                                            <span class="ml-cell-meta">
                                                <span class="ml-cell-mono">{{ $facility->code }}</span>
                                                <span class="ml-meta-dot">·</span>
                                                {{ $facility->type_label }}
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td data-label="Wilayah">
                                    <div>{{ $facility->city ?: '—' }}</div>
                                    <div class="ml-cell-meta">{{ $facility->province ?: 'Wilayah belum diisi' }}</div>
                                </td>
                                <td data-label="Status">
                                    @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
                                </td>
                                <td data-label="Tim">
                                    <span class="ml-team-count">{{ $facility->users_count }}</span>
                                    <span class="ml-cell-meta">akun</span>
                                </td>
                                <td data-label="Diperbarui" class="ml-cell-meta">
                                    {{ $facility->updated_at?->format('d M Y') ?? '—' }}
                                </td>
                                <td class="is-right">
                                    <a href="{{ route('admin.facilities.show', $facility) }}" class="ml-row-open">
                                        Buka
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
                {{ $facilities->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
