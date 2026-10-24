@extends('layouts.public', ['title' => 'Terima Undangan'])

@section('content')
<div class="pub-invite-shell">
    <aside class="pub-invite-aside">
        <p class="pub-eyebrow">Undangan tim</p>
        <h1>Bergabung dengan {{ $invitation->facility->name }}</h1>
        <p>
            Anda diundang sebagai <strong>{{ $roleLabel }}</strong>. Aktifkan akun untuk mulai mengelola
            stok dan koordinasi redistribusi fasilitas.
        </p>

        <div class="pub-invite-facility">
            <span class="pub-invite-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($invitation->facility->name, 0, 1)) }}</span>
            <span>
                <strong>{{ $invitation->facility->name }}</strong>
                <small>{{ $invitation->facility->city ?: 'Fasilitas kesehatan' }} · {{ $invitation->facility->type_label }}</small>
            </span>
        </div>

        <ul class="pub-invite-meta">
            <li>
                <span>Peran</span>
                <strong>{{ $roleLabel }}</strong>
            </li>
            <li>
                <span>Diundang oleh</span>
                <strong>{{ $invitation->inviter->name }}</strong>
            </li>
            <li>
                <span>Email</span>
                <strong>{{ $invitation->email }}</strong>
            </li>
            <li>
                <span>Berlaku sampai</span>
                <strong>{{ $invitation->expires_at->format('d M Y, H:i') }}</strong>
            </li>
        </ul>

        <div class="pub-invite-trust">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
                <path d="M9 12l2 2 4-4" />
            </svg>
            <span>Undangan ini hanya dapat digunakan satu kali. Jika tautan kedaluwarsa, minta undangan baru dari kepala fasilitas.</span>
        </div>
    </aside>

    <section class="pub-invite-form">
        <h2>Aktifkan akun</h2>
        <p>Isi nama lengkap dan buat password untuk menyelesaikan pendaftaran.</p>

        @if($errors->any())
            <div class="pub-alert pub-alert-error" role="alert">
                <strong>Periksa kembali isian berikut:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('invitations.accept', ['token' => $token]) }}" novalidate>
            @csrf

            <div class="pub-field">
                <label for="name" class="pub-label">Nama lengkap <span class="pub-required">*</span></label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    class="pub-input @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                    @if($errors->has('name')) aria-describedby="name-error" @endif
                >
                @error('name')<p class="pub-error" id="name-error">{{ $message }}</p>@enderror
            </div>

            <div class="pub-field">
                <label for="password" class="pub-label">Password <span class="pub-required">*</span></label>
                <div class="password-control">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="pub-input password-input @error('password') is-invalid @enderror"
                        required
                        autocomplete="new-password"
                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        @if($errors->has('password')) aria-describedby="password-error" @endif
                    >
                    <button type="button" class="password-toggle" data-password-toggle
                        data-password-target="password" data-label-show="Tampilkan password"
                        data-label-hide="Sembunyikan password" aria-controls="password"
                        aria-label="Tampilkan password" aria-pressed="false">
                        <svg class="password-toggle-show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />
                            <circle cx="12" cy="12" r="2.8" />
                        </svg>
                        <svg class="password-toggle-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path d="M3 3l18 18" />
                            <path d="M10.6 6.2A10.8 10.8 0 0112 6c6 0 9.5 6 9.5 6a16.5 16.5 0 01-3 3.6" />
                            <path d="M6.2 6.8C3.8 8.5 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8" />
                        </svg>
                    </button>
                </div>
                <p class="pub-help">Gunakan password yang kuat dan tidak dipakai di layanan lain.</p>
                @error('password')<p class="pub-error" id="password-error">{{ $message }}</p>@enderror
            </div>

            <div class="pub-field">
                <label for="password_confirmation" class="pub-label">Ulangi password <span class="pub-required">*</span></label>
                <div class="password-control">
                    <input id="password_confirmation" name="password_confirmation" type="password"
                        class="pub-input password-input" required autocomplete="new-password">
                    <button type="button" class="password-toggle" data-password-toggle
                        data-password-target="password_confirmation"
                        data-label-show="Tampilkan ulangi password"
                        data-label-hide="Sembunyikan ulangi password"
                        aria-controls="password_confirmation" aria-label="Tampilkan ulangi password"
                        aria-pressed="false">
                        <svg class="password-toggle-show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />
                            <circle cx="12" cy="12" r="2.8" />
                        </svg>
                        <svg class="password-toggle-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path d="M3 3l18 18" />
                            <path d="M10.6 6.2A10.8 10.8 0 0112 6c6 0 9.5 6 9.5 6a16.5 16.5 0 01-3 3.6" />
                            <path d="M6.2 6.8C3.8 8.5 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="pub-actions">
                <button type="submit" class="pub-btn pub-btn-primary pub-btn-block">Aktifkan akun</button>
            </div>
        </form>

        <p class="pub-invite-footnote">
            Setelah akun aktif, Anda dapat masuk menggunakan email {{ $invitation->email }}.
        </p>
    </section>
</div>
@endsection
