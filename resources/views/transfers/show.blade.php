@extends('layouts.mediloop', ['title' => 'Lacak Transfer #' . $transfer->transfer_code])

@section('content')
@php
    $statusLabels = [
        'PROPOSED' => ['label' => 'Menunggu otorisasi donor', 'tone' => 'pending'],
        'APPROVED' => ['label' => 'Menunggu kesediaan penerima', 'tone' => 'active'],
        'ACCEPTED' => ['label' => 'Siap kirim', 'tone' => 'active'],
        'IN_TRANSIT' => ['label' => 'Dalam pengiriman', 'tone' => 'transit'],
        'COMPLETED' => ['label' => 'Selesai', 'tone' => 'inactive'],
        'CANCELLED' => ['label' => 'Dibatalkan', 'tone' => 'suspended'],
    ];
    $statusMeta = $statusLabels[$transfer->status] ?? ['label' => $transfer->status, 'tone' => 'inactive'];
    $stages = [
        'PROPOSED' => ['label' => 'Pengusulan', 'time' => $transfer->proposed_at],
        'APPROVED' => ['label' => 'Otorisasi donor', 'time' => $transfer->approved_at],
        'ACCEPTED' => ['label' => 'Kesediaan penerima', 'time' => $transfer->accepted_at],
        'IN_TRANSIT' => ['label' => 'Pengiriman', 'time' => $transfer->shipped_at],
        'COMPLETED' => ['label' => 'Penerimaan & mutasi', 'time' => $transfer->completed_at],
    ];
    $statusOrder = ['PROPOSED' => 1, 'APPROVED' => 2, 'ACCEPTED' => 3, 'IN_TRANSIT' => 4, 'COMPLETED' => 5];
    $currentRank = $statusOrder[$transfer->status] ?? 0;
    $isDonorStaff = $user->isSysadmin() || $user->facility_id === $transfer->donor_facility_id;
    $isRecipientStaff = $user->isSysadmin() || $user->facility_id === $transfer->recipient_facility_id;
    $canApprove = $isDonorStaff && $user->canApproveDonorTransfer();
    $canAccept = $isRecipientStaff && $user->canAcceptRecipientTransfer();
    $canDispatch = $user->isSysadmin() || ($user->facility_id === $transfer->donor_facility_id && $user->hasFacilityRole());
    $canComplete = $isRecipientStaff && $user->canConfirmPhysicalArrival();
    $canCancel = $transfer->status !== 'COMPLETED'
        && $transfer->status !== 'CANCELLED'
        && ($user->isSysadmin() || in_array($user->facility_id, [$transfer->donor_facility_id, $transfer->recipient_facility_id], true));
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('transfers.index') }}">Redistribusi</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ $transfer->transfer_code }}</span>
    </nav>

    @if(session('status'))
        <div class="ml-alert ml-alert-success" role="status">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="ml-alert ml-alert-error" role="alert">
            <strong>Periksa kembali isian berikut:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <header class="ml-passport">
        <div class="ml-passport-main">
            <span class="ml-passport-mark" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h12l3 3-3 3H4" />
                    <path d="M20 17H8l-3-3 3-3" />
                </svg>
            </span>
            <div class="ml-passport-copy">
                <p class="ml-passport-eyebrow">Transfer obat · {{ $transfer->transfer_code }}</p>
                <div class="ml-passport-title-row">
                    <h1 class="ml-page-title">{{ $transfer->batch->medicine->name }}</h1>
                    <span class="ml-status ml-status-{{ $statusMeta['tone'] }}">{{ $statusMeta['label'] }}</span>
                </div>
                <p class="ml-passport-meta">
                    {{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}
                    <span class="ml-meta-dot">·</span>
                    Diajukan {{ $transfer->created_at->format('d M Y, H:i') }}
                    <span class="ml-meta-dot">·</span>
                    Skor {{ $transfer->match_score }}%
                </p>
            </div>
        </div>
        <div class="ml-passport-actions">
            <a href="{{ route('transfers.index') }}" class="ml-btn-quiet">Kembali ke daftar</a>
        </div>
    </header>

    <section class="ml-card" aria-labelledby="progress-title">
        <div class="ml-card-head ml-card-head-split">
            <div>
                <h2 class="ml-card-title" id="progress-title">Progres transfer</h2>
                <p class="ml-card-sub">Setiap tahap memerlukan otorisasi pihak yang berwenang.</p>
            </div>
            <span class="ml-card-index" aria-hidden="true">5 TAHAP</span>
        </div>
        <div class="ml-card-body">
            <ol class="ml-transfer-stages">
                @foreach($stages as $statusKey => $stage)
                    @php
                        $stageRank = $statusOrder[$statusKey];
                        $isDone = $currentRank >= $stageRank;
                        $isCurrent = $transfer->status === $statusKey;
                    @endphp
                    <li class="ml-transfer-stage {{ $isDone ? 'is-done' : '' }} {{ $isCurrent ? 'is-current' : '' }}">
                        <span class="ml-transfer-stage-dot" aria-hidden="true">
                            @if($isDone)
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5" /></svg>
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </span>
                        <span class="ml-transfer-stage-copy">
                            <span class="ml-transfer-stage-label">{{ $stage['label'] }}</span>
                            <span class="ml-transfer-stage-time">{{ $stage['time'] ? $stage['time']->format('d M Y, H:i') : 'Menunggu' }}</span>
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <div class="ml-two-col">
        <div>
            @if($transfer->status !== 'COMPLETED')
                <div class="ml-alert ml-alert-warning ml-invariant-note" role="status">
                    <strong>Stok donor belum dipotong</strong>
                    <p>
                        Mutasi stok donor dan penerima hanya dijalankan saat penerima mengonfirmasi
                        penerimaan fisik. Sebelum tahap itu, stok donor tetap utuh.
                    </p>
                </div>
            @else
                <div class="ml-alert ml-alert-success ml-invariant-note" role="status">
                    <strong>Mutasi stok selesai</strong>
                    <p>
                        {{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}
                        telah dipotong dari {{ $transfer->donorFacility->name }} dan ditambahkan ke
                        {{ $transfer->recipientFacility->name }} pada {{ $transfer->completed_at?->format('d M Y, H:i') }}.
                    </p>
                </div>
            @endif

            @if($transfer->status !== 'COMPLETED' && $transfer->status !== 'CANCELLED')
                <section class="ml-card ml-action-panel" aria-labelledby="action-title">
                    <div class="ml-card-head ml-card-head-split">
                        <div>
                            <h2 class="ml-card-title" id="action-title">Tindakan tahap ini</h2>
                            <p class="ml-card-sub">{{ $user->name }} · {{ $user->job_title }}</p>
                        </div>
                        <span class="ml-role-chip">{{ \App\Enums\UserRole::tryFrom($user->role)?->label() ?? $user->role }}</span>
                    </div>
                    <div class="ml-card-body">
                        @if($transfer->status === 'PROPOSED')
                            <p class="ml-action-copy">Menunggu persetujuan Kepala Instalasi Farmasi fasilitas donor.</p>
                            @if($canApprove)
                                <form method="POST" action="{{ route('transfers.approve', $transfer) }}">
                                    @csrf
                                    <button type="submit" class="ml-btn-primary">Setujui pengeluaran obat</button>
                                </form>
                            @else
                                <p class="ml-action-note">Hanya Kepala Instalasi Farmasi fasilitas donor yang berwenang menyetujui tahap ini.</p>
                            @endif
                        @elseif($transfer->status === 'APPROVED')
                            <p class="ml-action-copy">Donor telah menyetujui. Menunggu kesediaan menerima dari fasilitas penerima.</p>
                            @if($canAccept)
                                <form method="POST" action="{{ route('transfers.accept', $transfer) }}">
                                    @csrf
                                    <button type="submit" class="ml-btn-primary">Konfirmasi kesediaan menerima</button>
                                </form>
                            @else
                                <p class="ml-action-note">Hanya Apoteker atau Kepala Fasilitas penerima yang berwenang.</p>
                            @endif
                        @elseif($transfer->status === 'ACCEPTED')
                            <p class="ml-action-copy">Penerima siap. Masukkan rincian kurir untuk memberangkatkan obat.</p>
                            @if($canDispatch)
                                <form method="POST" action="{{ route('transfers.dispatch', $transfer) }}">
                                    @csrf
                                    <div class="ml-form-grid">
                                        <div class="ml-field">
                                            <label for="courier_type" class="ml-label">Jenis kurir / transportasi</label>
                                            <input id="courier_type" name="courier_type" type="text" class="ml-input" value="Kurir farmasi berpendingin (cold-box)" required>
                                        </div>
                                        <div class="ml-field">
                                            <label for="tracking_number" class="ml-label">Nomor resi / surat jalan</label>
                                            <input id="tracking_number" name="tracking_number" type="text" class="ml-input is-mono" value="EXP-DKI-{{ random_int(1000, 9999) }}" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="ml-btn-primary">Berangkatkan pengiriman</button>
                                </form>
                            @else
                                <p class="ml-action-note">Hanya staf fasilitas donor yang dapat memberangkatkan pengiriman.</p>
                            @endif
                        @elseif($transfer->status === 'IN_TRANSIT')
                            <div class="ml-transit-banner">
                                <span class="ml-detail-label">Sedang dalam perjalanan</span>
                                <div class="ml-transit-meta">
                                    <span>Resi <strong>{{ $transfer->tracking_number }}</strong></span>
                                    <span>{{ $transfer->courier_type }}</span>
                                </div>
                            </div>
                            <p class="ml-action-copy">
                                Penerima memeriksa kondisi fisik, segel kemasan, dan suhu dingin sebelum mengonfirmasi penerimaan.
                            </p>
                            @if($canComplete)
                                <form method="POST" action="{{ route('transfers.complete', $transfer) }}">
                                    @csrf
                                    <button type="submit" class="ml-btn-primary">Konfirmasi penerimaan fisik &amp; mutasi stok</button>
                                </form>
                            @else
                                <p class="ml-action-note">Menunggu konfirmasi Kepala Fasilitas penerima sebelum stok dimutasi.</p>
                            @endif
                        @endif

                        @if($canCancel)
                            <div class="ml-action-cancel">
                                <form method="POST" action="{{ route('transfers.cancel', $transfer) }}" onsubmit="return confirm('Batalkan transfer ini? Tindakan tidak dapat dibatalkan.');">
                                    @csrf
                                    <button type="submit" class="ml-link-action ml-link-danger">Batalkan transfer</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            <section class="ml-card" aria-labelledby="details-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="details-title">Detail logistik</h2>
                        <p class="ml-card-sub">Rute, jumlah, dan hasil pencocokan kandidat.</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">DATA</span>
                </div>
                <div class="ml-card-body">
                    <div class="ml-detail-grid">
                        <div>
                            <div class="ml-detail-label">Batch</div>
                            <div class="ml-detail-value is-mono">{{ $transfer->batch->batch_number }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Jumlah</div>
                            <div class="ml-detail-value">{{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Donor</div>
                            <div class="ml-detail-value">{{ $transfer->donorFacility->name }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Penerima</div>
                            <div class="ml-detail-value">{{ $transfer->recipientFacility->name }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Jarak udara</div>
                            <div class="ml-detail-value">{{ $transfer->distance_km }} km</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Batas serapan penerima</div>
                            <div class="ml-detail-value">{{ $transfer->recipient_intake_cap }} unit</div>
                        </div>
                        <div class="is-full">
                            <div class="ml-detail-label">Catatan</div>
                            <div class="ml-detail-value">{{ $transfer->notes ?: 'Tidak ada catatan tambahan.' }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="audit-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="audit-title">Jejak audit</h2>
                        <p class="ml-card-sub">Riwayat otorisasi dan perubahan status.</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">LOG</span>
                </div>
                @if($transfer->auditLogs->isEmpty())
                    <div class="ml-empty-state">
                        <p class="ml-empty-title">Belum ada catatan audit</p>
                        <p class="ml-empty-copy">Aktivitas transfer akan muncul di sini.</p>
                    </div>
                @else
                    <ol class="ml-audit-list">
                        @foreach($transfer->auditLogs as $log)
                            <li class="ml-audit-item">
                                <span class="ml-audit-dot" aria-hidden="true"></span>
                                <div class="ml-audit-time">{{ $log->created_at->format('d M Y · H:i') }}</div>
                                <div class="ml-audit-body">
                                    <strong>{{ $log->actor->name ?? 'Sistem' }}</strong>
                                    <span>{{ $log->description }}</span>
                                    <code>{{ $log->action }}</code>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        </div>

        <aside>
            <section class="ml-card ml-sticky" aria-labelledby="summary-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="summary-title">Ringkasan transfer</h2>
                </div>
                <div class="ml-card-body">
                    <div class="ml-route-vertical">
                        <div>
                            <span class="ml-detail-label">Donor</span>
                            <div class="ml-detail-value">{{ $transfer->donorFacility->name }}</div>
                        </div>
                        <span class="ml-route-line" aria-hidden="true"></span>
                        <div>
                            <span class="ml-detail-label">Penerima</span>
                            <div class="ml-detail-value">{{ $transfer->recipientFacility->name }}</div>
                        </div>
                    </div>

                    <div class="ml-score-list">
                        <div class="ml-score-row">
                            <span>Kebutuhan (50%)</span>
                            <strong>{{ $transfer->need_score }}%</strong>
                        </div>
                        <div class="ml-score-bar" aria-hidden="true"><span style="width: {{ $transfer->need_score }}%"></span></div>
                        <div class="ml-score-row">
                            <span>Jarak (30%)</span>
                            <strong>{{ $transfer->proximity_score }}%</strong>
                        </div>
                        <div class="ml-score-bar" aria-hidden="true"><span style="width: {{ $transfer->proximity_score }}%"></span></div>
                        <div class="ml-score-row">
                            <span>Logistik (20%)</span>
                            <strong>{{ $transfer->logistics_score }}%</strong>
                        </div>
                        <div class="ml-score-bar" aria-hidden="true"><span style="width: {{ $transfer->logistics_score }}%"></span></div>
                        <div class="ml-score-total">
                            <span>Skor kecocokan</span>
                            <strong>{{ $transfer->match_score }}%</strong>
                        </div>
                    </div>

                    <p class="ml-help">Rekomendasi bersifat saran. Setiap transfer memerlukan persetujuan petugas fasilitas.</p>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
