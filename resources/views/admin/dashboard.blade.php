@extends('layouts.mediloop', ['title' => 'Administrasi'])

@section('content')
<div class="ml-page-admin">
    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">Ringkasan administrasi</h1>
            <p class="ml-page-sub">Antrean verifikasi dan kesehatan registry fasilitas MediLoop.</p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('admin.facilities.index') }}" class="ml-btn-quiet">Buka registry</a>
            <a href="{{ route('admin.facility-requests.index') }}" class="ml-btn-primary">Tinjau pengajuan</a>
        </div>
    </header>

    <div class="ml-summary-bar" aria-label="Ringkasan platform">
        <div class="ml-summary-item">
            <span class="ml-summary-value">{{ number_format($counts['active'], 0, ',', '.') }}</span>
            <span class="ml-summary-label">Fasilitas aktif</span>
        </div>
        <div class="ml-summary-item">
            <span class="ml-summary-value">{{ number_format($counts['pendingRequests'], 0, ',', '.') }}</span>
            <span class="ml-summary-label">Pengajuan menunggu</span>
        </div>
        <div class="ml-summary-item">
            <span class="ml-summary-value">{{ number_format($counts['pendingFacilities'], 0, ',', '.') }}</span>
            <span class="ml-summary-label">Faskes belum terverifikasi</span>
        </div>
        <div class="ml-summary-item">
            <span class="ml-summary-value">{{ number_format($counts['users'], 0, ',', '.') }}</span>
            <span class="ml-summary-label">Akun pengguna</span>
        </div>
    </div>

    <div class="ml-two-col">
        <section class="ml-card" aria-labelledby="queue-title">
            <div class="ml-card-head" style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                <div>
                    <h2 class="ml-card-title" id="queue-title">Antrean pengajuan</h2>
                    <p class="ml-card-sub">Pengajuan fasilitas terbaru yang menunggu keputusan.</p>
                </div>
                <a href="{{ route('admin.facility-requests.index') }}" class="ml-link-action">Lihat semua</a>
            </div>

            @if($pendingRequests->isEmpty())
                <div class="ml-empty-state">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    <p class="ml-empty-title">Antrean bersih</p>
                    <p class="ml-empty-copy">Tidak ada pengajuan yang menunggu review saat ini.</p>
                </div>
            @else
                <div class="ml-table-responsive">
                    <table class="ml-table">
                        <caption class="ml-sr-only">Pengajuan fasilitas terbaru</caption>
                        <thead>
                            <tr>
                                <th scope="col">Fasilitas</th>
                                <th scope="col">Diajukan</th>
                                <th scope="col"><span class="ml-sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRequests as $requestRecord)
                                <tr>
                                    <td>
                                        <div class="ml-cell-title">{{ $requestRecord->name }}</div>
                                        <div class="ml-cell-meta">{{ \App\Enums\FacilityType::tryFrom($requestRecord->type)?->label() ?? $requestRecord->type }} · {{ $requestRecord->city }}</div>
                                    </td>
                                    <td class="ml-cell-meta" style="white-space: nowrap;">{{ $requestRecord->created_at->diffForHumans() }}</td>
                                    <td class="is-right">
                                        <a href="{{ route('admin.facility-requests.show', $requestRecord) }}" class="ml-btn-quiet ml-btn-sm">Review</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="ml-card" aria-labelledby="recent-title">
            <div class="ml-card-head">
                <h2 class="ml-card-title" id="recent-title">Fasilitas terbaru</h2>
                <p class="ml-card-sub">Lima entri terakhir di registry.</p>
            </div>
            <div class="ml-card-body">
                @forelse($recentFacilities as $facility)
                    <div style="display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--ml-border);">
                        <div>
                            <a href="{{ route('admin.facilities.show', $facility) }}" class="ml-cell-title" style="text-decoration: none;">{{ $facility->name }}</a>
                            <div class="ml-cell-meta">{{ $facility->type_label }} · {{ $facility->city }}</div>
                        </div>
                        @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
                    </div>
                @empty
                    <p class="ml-help" style="margin-top: 0;">Belum ada fasilitas di registry.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
