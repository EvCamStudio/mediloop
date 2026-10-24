@extends('layouts.mediloop', ['title' => $facility->name])

@section('content')
@php
    $completedChecks = collect($verificationChecklist)->where('complete', true)->count();
    $totalChecks = count($verificationChecklist);
    $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($facility->name, 0, 1));
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Administrasi</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('admin.facilities.index') }}">Fasilitas</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ $facility->name }}</span>
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
            <span class="ml-passport-mark" aria-hidden="true">{{ $initials }}</span>
            <div class="ml-passport-copy">
                <p class="ml-passport-eyebrow">{{ $facility->type_label }} · {{ $facility->ownership ? ucfirst(strtolower($facility->ownership)) : 'Kepemilikan belum diisi' }}</p>
                <div class="ml-passport-title-row">
                    <h1 class="ml-page-title">{{ $facility->name }}</h1>
                    @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
                </div>
                <p class="ml-passport-meta">
                    <span class="ml-cell-mono">{{ $facility->code }}</span>
                    <span class="ml-meta-dot">·</span>
                    {{ collect([$facility->city, $facility->province])->filter()->implode(', ') ?: 'Wilayah belum diisi' }}
                    @if($facility->updated_at)
                        <span class="ml-meta-dot">·</span>
                        Diperbarui {{ $facility->updated_at->format('d M Y') }}
                    @endif
                </p>
            </div>
        </div>
        <div class="ml-passport-actions">
            <a href="{{ route('admin.facilities.edit', $facility) }}" class="ml-btn-quiet">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 20h9" />
                    <path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z" />
                </svg>
                Edit data
            </a>
            @if($facility->status === \App\Enums\FacilityStatus::Pending->value)
                <form method="POST" action="{{ route('admin.facilities.verify', $facility) }}">
                    @csrf
                    <button type="submit" class="ml-btn-primary">Verifikasi fasilitas</button>
                </form>
            @elseif($facility->status === \App\Enums\FacilityStatus::Suspended->value)
                <form method="POST" action="{{ route('admin.facilities.reactivate', $facility) }}">
                    @csrf
                    <button type="submit" class="ml-btn-primary">Aktifkan kembali</button>
                </form>
            @elseif($facility->status === \App\Enums\FacilityStatus::Active->value)
                <form method="POST" action="{{ route('admin.facilities.suspend', $facility) }}" onsubmit="return confirm('Tangguhkan fasilitas ini? Anggota tim tidak dapat mengakses fitur operasional sampai diaktifkan kembali.');">
                    @csrf
                    <button type="submit" class="ml-btn-danger-quiet">Tangguhkan</button>
                </form>
            @endif
        </div>
    </header>

    <div class="ml-two-col">
        <div>
            <section class="ml-card" aria-labelledby="profile-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="profile-title">Identitas &amp; operasional</h2>
                        <p class="ml-card-sub">Data yang dipakai pada pencocokan dan audit redistribusi.</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">01</span>
                </div>
                <div class="ml-card-body">
                    <div class="ml-detail-grid">
                        <div>
                            <div class="ml-detail-label">Kode fasilitas</div>
                            <div class="ml-detail-value is-mono">{{ $facility->code }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Jenis</div>
                            <div class="ml-detail-value">{{ $facility->type_label }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Kepemilikan</div>
                            <div class="ml-detail-value">{{ $facility->ownership ? ucfirst(strtolower($facility->ownership)) : '—' }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Nomor izin</div>
                            <div class="ml-detail-value">{{ $facility->license_number ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Penanggung jawab</div>
                            <div class="ml-detail-value">{{ $facility->contact_person ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Telepon</div>
                            <div class="ml-detail-value">{{ $facility->phone ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Email fasilitas</div>
                            <div class="ml-detail-value">{{ $facility->email ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="ml-detail-label">Rantai dingin</div>
                            <div class="ml-detail-value">
                                <span class="ml-inline-status {{ $facility->cold_chain_ready ? 'is-on' : 'is-off' }}">
                                    {{ $facility->cold_chain_ready ? 'Tersedia (2–8°C)' : 'Tidak tersedia' }}
                                </span>
                            </div>
                        </div>
                        <div class="is-full">
                            <div class="ml-detail-label">Alamat</div>
                            <div class="ml-detail-value">
                                {{ $facility->address }}
                                @if($facility->district) · {{ $facility->district }} @endif
                                · {{ collect([$facility->city, $facility->province])->filter()->implode(', ') }}
                            </div>
                        </div>
                        @if($facility->notes)
                            <div class="is-full">
                                <div class="ml-detail-label">Catatan verifikasi</div>
                                <div class="ml-detail-value">{{ $facility->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="location-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="location-title">Lokasi operasional</h2>
                        <p class="ml-card-sub">Titik koordinat yang dipakai untuk menghitung jarak antar fasilitas.</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">02</span>
                </div>
                <div class="ml-card-body">
                    <div id="facility-map" class="ml-map-picker" data-lat="{{ $facility->latitude }}" data-lng="{{ $facility->longitude }}" data-readonly="true" role="application" tabindex="0" aria-label="Peta lokasi fasilitas" aria-describedby="map-coordinates"></div>
                    <div class="ml-map-coordinates">
                        <span>
                            <span class="ml-detail-label">Koordinat</span>
                            {{ collect([$facility->city, $facility->province])->filter()->implode(', ') ?: 'Wilayah belum diisi' }}
                        </span>
                        <code id="map-coordinates">{{ number_format((float) $facility->latitude, 7, '.', '') }}, {{ number_format((float) $facility->longitude, 7, '.', '') }}</code>
                    </div>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="audit-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="audit-title">Jejak audit</h2>
                        <p class="ml-card-sub">Aktivitas terbaru untuk fasilitas ini.</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">03</span>
                </div>
                @if($auditLogs->isEmpty())
                    <div class="ml-empty-state">
                        <p class="ml-empty-title">Belum ada aktivitas</p>
                        <p class="ml-empty-copy">Aktivitas verifikasi dan tim akan muncul di sini.</p>
                    </div>
                @else
                    <ol class="ml-audit-list">
                        @foreach($auditLogs as $log)
                            <li class="ml-audit-item">
                                <span class="ml-audit-dot" aria-hidden="true"></span>
                                <div class="ml-audit-time">{{ $log->created_at?->format('d M Y · H:i') }}</div>
                                <div class="ml-audit-body">
                                    <strong>{{ $log->actor?->name ?? 'Sistem' }}</strong>
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
            <section class="ml-card ml-verification-card" aria-labelledby="verification-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="verification-title">Kesiapan verifikasi</h2>
                    <p class="ml-card-sub">{{ $completedChecks }} dari {{ $totalChecks }} dokumen inti tersedia.</p>
                </div>
                <div class="ml-card-body">
                    <div class="ml-verification-status">
                        @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
                        <span class="ml-verification-date">
                            @if($facility->verified_at)
                                Diverifikasi {{ $facility->verified_at->format('d M Y') }}
                            @else
                                Belum diverifikasi
                            @endif
                        </span>
                    </div>

                    <ul class="ml-checklist">
                        @foreach($verificationChecklist as $item)
                            <li class="ml-checklist-item {{ $item['complete'] ? 'is-complete' : '' }}">
                                <span class="ml-checklist-icon" aria-hidden="true">
                                    @if($item['complete'])
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6L9 17l-5-5" /></svg>
                                    @else
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v5m0 3h.01" /><circle cx="12" cy="12" r="9" /></svg>
                                    @endif
                                </span>
                                <span>{{ $item['label'] }}</span>
                                <span class="ml-sr-only">{{ $item['complete'] ? 'Lengkap' : 'Belum lengkap' }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="ml-verification-meta">
                        <div>
                            <span class="ml-detail-label">Sumber data</span>
                            @if($requestRecord)
                                <a href="{{ route('admin.facility-requests.show', $requestRecord) }}" class="ml-link-action">Pengajuan fasilitas</a>
                                <div class="ml-cell-meta">{{ $requestRecord->created_at->format('d M Y') }}</div>
                            @else
                                <div class="ml-detail-value">Input administrator</div>
                            @endif
                        </div>
                        @if($facility->verifiedBy)
                            <div>
                                <span class="ml-detail-label">Diverifikasi oleh</span>
                                <div class="ml-detail-value">{{ $facility->verifiedBy->name }}</div>
                            </div>
                        @endif
                    </div>

                    @if($facility->status === \App\Enums\FacilityStatus::Pending->value)
                        <form method="POST" action="{{ route('admin.facilities.verify', $facility) }}" class="ml-verification-action">
                            @csrf
                            <button type="submit" class="ml-btn-primary">Verifikasi &amp; aktifkan</button>
                        </form>
                        <p class="ml-help">Pastikan izin, alamat, dan koordinat sudah sesuai sebelum mengaktifkan.</p>
                    @endif
                </div>
            </section>

            <section class="ml-card" aria-labelledby="team-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="team-title">Tim fasilitas</h2>
                    <p class="ml-card-sub">{{ $facility->users->count() }} akun terhubung.</p>
                </div>
                <div class="ml-card-body">
                    @if($facility->users->isEmpty())
                        <p class="ml-help" style="margin-top: 0;">Belum ada akun. Undang kepala fasilitas setelah verifikasi.</p>
                    @else
                        <div class="ml-member-list">
                            @foreach($facility->users as $member)
                                <div class="ml-member-row">
                                    <span class="ml-member-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($member->name, 0, 1)) }}</span>
                                    <div class="ml-member-copy">
                                        <div class="ml-cell-title">{{ $member->name }}</div>
                                        <div class="ml-cell-meta">{{ $member->role_label }} · {{ $member->email }}</div>
                                    </div>
                                    @include('admin.partials.status', ['status' => $member->status, 'label' => \App\Enums\UserStatus::tryFrom($member->status)?->label() ?? $member->status])
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($invitations->isNotEmpty())
                        <div class="ml-invitation-block">
                            <div class="ml-detail-label">Undangan menunggu</div>
                            @foreach($invitations as $invitation)
                                <div class="ml-invitation-row">
                                    <div>
                                        <div class="ml-detail-value">{{ $invitation->email }}</div>
                                        <div class="ml-cell-meta">
                                            {{ \App\Enums\UserRole::tryFrom($invitation->role)?->label() ?? $invitation->role }}
                                            · berlaku sampai {{ $invitation->expires_at->format('d M Y') }}
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.facilities.invitations.destroy', [$facility, $invitation]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-link-action ml-link-danger">Batalkan</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($facility->isActive())
                        <form method="POST" action="{{ route('admin.facilities.invitations.store', $facility) }}" class="ml-invite-form">
                            @csrf
                            <div class="ml-detail-label">Undang anggota baru</div>
                            <div class="ml-field">
                                <label for="invite_email" class="ml-label">Email anggota</label>
                                <input id="invite_email" name="email" type="email" class="ml-input" placeholder="nama@faskes.id" required autocomplete="email">
                            </div>
                            <div class="ml-field">
                                <label for="invite_role" class="ml-label">Peran</label>
                                <select id="invite_role" name="role" class="ml-select">
                                    <option value="FACILITY_APPROVER">Kepala Fasilitas</option>
                                    <option value="FACILITY_STAFF">Staf Farmasi</option>
                                </select>
                            </div>
                            <button type="submit" class="ml-btn-quiet">Kirim undangan</button>
                        </form>
                    @else
                        <p class="ml-alert ml-alert-info" style="margin: 18px 0 0;">
                            Fasilitas harus aktif sebelum mengundang anggota tim.
                        </p>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/js/facility-map-picker.js')
@endsection
