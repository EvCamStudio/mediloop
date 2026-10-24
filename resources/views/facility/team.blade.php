@extends('layouts.mediloop', ['title' => 'Tim Fasilitas'])

@section('content')
<div class="ml-page-admin">
    <header class="ml-passport">
        <div class="ml-passport-main">
            <span class="ml-passport-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($facility->name, 0, 1)) }}</span>
            <div class="ml-passport-copy">
                <p class="ml-passport-eyebrow">Tim fasilitas</p>
                <div class="ml-passport-title-row">
                    <h1 class="ml-page-title">{{ $facility->name }}</h1>
                    @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
                </div>
                <p class="ml-passport-meta">Kelola akses staf farmasi dan kepala fasilitas melalui undangan.</p>
            </div>
        </div>
        <div class="ml-passport-actions">
            <a href="{{ route('facility.settings.edit') }}" class="ml-btn-quiet">Profil fasilitas</a>
        </div>
    </header>

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

    @if(! $facility->isActive())
        <div class="ml-alert ml-alert-info" role="status">
            Fasilitas belum aktif. Undangan baru dapat dikirim setelah administrator memverifikasi fasilitas.
        </div>
    @endif

    <div class="ml-inline-metrics" aria-label="Ringkasan tim">
        <div class="ml-inline-metric">
            <span class="ml-inline-metric-value">{{ number_format($members->count(), 0, ',', '.') }}</span>
            <span class="ml-inline-metric-label">Akun terhubung</span>
        </div>
        <div class="ml-inline-metric">
            <span class="ml-inline-metric-value">{{ number_format($invitations->count(), 0, ',', '.') }}</span>
            <span class="ml-inline-metric-label">Undangan menunggu</span>
        </div>
    </div>

    <div class="ml-two-col">
        <div>
            <section class="ml-card" aria-labelledby="members-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="members-title">Anggota tim</h2>
                    <p class="ml-card-sub">Semua akun yang dapat mengakses data fasilitas ini.</p>
                </div>
                @if($members->isEmpty())
                    <div class="ml-empty-state">
                        <p class="ml-empty-title">Belum ada anggota</p>
                        <p class="ml-empty-copy">Kirim undangan pertama melalui formulir di samping.</p>
                    </div>
                @else
                    <div class="ml-table-responsive">
                        <table class="ml-table ml-table-airy ml-table-team">
                            <caption class="ml-sr-only">Anggota tim fasilitas</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Peran</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Bergabung</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            <div class="ml-member-row">
                                                <span class="ml-member-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($member->name, 0, 1)) }}</span>
                                                <span class="ml-member-copy">
                                                    <span class="ml-cell-title">{{ $member->name }}</span>
                                                    <span class="ml-cell-meta">{{ $member->email }}</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td data-label="Peran">{{ $member->role_label }}</td>
                                        <td data-label="Status">@include('admin.partials.status', ['status' => $member->status, 'label' => \App\Enums\UserStatus::tryFrom($member->status)?->label() ?? $member->status])</td>
                                        <td class="ml-cell-meta" data-label="Bergabung">{{ ($member->joined_at ?? $member->created_at)?->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="ml-card" aria-labelledby="invitations-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="invitations-title">Undangan menunggu</h2>
                    <p class="ml-card-sub">Tautan undangan berlaku 7 hari dan hanya dapat digunakan sekali.</p>
                </div>
                @if($invitations->isEmpty())
                    <div class="ml-card-body">
                        <p class="ml-help" style="margin: 0;">Tidak ada undangan yang menunggu.</p>
                    </div>
                @else
                    <div class="ml-table-responsive">
                        <table class="ml-table ml-table-airy ml-table-team">
                            <caption class="ml-sr-only">Undangan tim yang belum diterima</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Email</th>
                                    <th scope="col">Peran</th>
                                    <th scope="col">Berlaku sampai</th>
                                    <th scope="col"><span class="ml-sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invitations as $invitation)
                                    <tr>
                                        <td>{{ $invitation->email }}</td>
                                        <td data-label="Peran">{{ \App\Enums\UserRole::tryFrom($invitation->role)?->label() ?? $invitation->role }}</td>
                                        <td class="ml-cell-meta" data-label="Berlaku sampai">{{ $invitation->expires_at->format('d M Y') }}</td>
                                        <td class="is-right">
                                            <form method="POST" action="{{ route('facility.team.invitations.destroy', $invitation) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-link-action ml-link-danger">Batalkan</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <aside>
            <section class="ml-card ml-sticky" aria-labelledby="invite-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="invite-title">Undang anggota</h2>
                    <p class="ml-card-sub">Kirim akses ke staf yang sudah diverifikasi secara internal.</p>
                </div>
                <div class="ml-card-body">
                    @if($facility->isActive())
                        <form method="POST" action="{{ route('facility.team.invite') }}">
                            @csrf
                            <div class="ml-field">
                                <label for="email" class="ml-label">Email <span class="ml-required">*</span></label>
                                <input id="email" name="email" type="email" class="ml-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@faskes.id" required>
                                @error('email')<p class="ml-error-text">{{ $message }}</p>@enderror
                            </div>

                            <div class="ml-field">
                                <label for="role" class="ml-label">Peran <span class="ml-required">*</span></label>
                                <select id="role" name="role" class="ml-select @error('role') is-invalid @enderror" required>
                                    @foreach($roles as $value => $label)
                                        <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('role')<p class="ml-error-text">{{ $message }}</p>@enderror
                            </div>

                            <button type="submit" class="ml-btn-primary ml-btn-block">Kirim undangan</button>
                        </form>
                    @else
                        <p class="ml-help" style="margin-top: 0;">Formulir undangan tersedia setelah fasilitas aktif.</p>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
