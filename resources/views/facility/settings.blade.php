@extends('layouts.mediloop', ['title' => 'Profil Fasilitas'])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Profil fasilitas</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Profil {{ $facility->name }}</h1>
                @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
            </div>
            <p class="ml-page-sub">Perbarui alamat dan kontak operasional. Perubahan identitas atau koordinat dilakukan administrator.</p>
        </div>
        <div class="ml-page-actions">
            <a href="{{ route('facility.team.index') }}" class="ml-btn-quiet">Kelola tim</a>
        </div>
    </header>

    @if(session('status'))
        <x-alert variant="success">{{ session('status') }}</x-alert>
    @endif

    <x-form.summary for="facilitySettingsForm" />

    <div class="ml-settings-layout">
        <aside>
            <section class="ml-card ml-settings-aside" aria-labelledby="identity-title">
                <div class="ml-card-body">
                    <span class="ml-settings-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($facility->name, 0, 1)) }}</span>
                    <h2 class="ml-card-title" id="identity-title">Identitas terverifikasi</h2>
                    <p class="ml-card-sub">Data ini dikunci setelah verifikasi. Hubungi administrator platform bila perlu diubah.</p>

                    <dl class="ml-settings-list">
                        <div>
                            <dt>Nama fasilitas</dt>
                            <dd>{{ $facility->name }}</dd>
                        </div>
                        <div>
                            <dt>Kode</dt>
                            <dd class="is-mono">{{ $facility->code }}</dd>
                        </div>
                        <div>
                            <dt>Jenis</dt>
                            <dd>{{ $facility->type_label }}</dd>
                        </div>
                        <div>
                            <dt>Nomor izin</dt>
                            <dd>{{ $facility->license_number ?: 'Belum diisi' }}</dd>
                        </div>
                        <div>
                            <dt>Koordinat</dt>
                            <dd class="is-mono">{{ number_format((float) $facility->latitude, 7, '.', '') }}, {{ number_format((float) $facility->longitude, 7, '.', '') }}</dd>
                        </div>
                        <div>
                            <dt>Diverifikasi</dt>
                            <dd>{{ $facility->verified_at?->format('d M Y') ?? 'Belum diverifikasi' }}</dd>
                        </div>
                    </dl>

                    <div class="ml-settings-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v4m0 4h.01" />
                        </svg>
                        Perubahan nama, jenis, izin, dan koordinat hanya melalui administrator.
                    </div>
                </div>
            </section>
        </aside>

        <form method="POST" action="{{ route('facility.settings.update') }}" id="facilitySettingsForm" novalidate data-validate>
            @csrf
            @method('PUT')

            <section class="ml-card" aria-labelledby="contact-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="contact-title">Alamat &amp; kontak</h2>
                        <p class="ml-card-sub">Data ini dipakai saat koordinasi transfer dan audit.</p>
                    </div>
                    <span class="ml-card-index" aria-hidden="true">EDIT</span>
                </div>
                <div class="ml-card-body">
                    <div class="ml-form-grid">
                        <div class="ml-field is-full">
                            <label for="address" class="ml-label">Alamat <span class="ml-required">*</span></label>
                            <input id="address" name="address" type="text" class="ml-input @error('address') is-invalid @enderror" value="{{ old('address', $facility->address) }}" required autocomplete="street-address" data-error-required="Alamat fasilitas wajib diisi.">
                            <x-form.error for="address" />
                        </div>

                        <div class="ml-field">
                            <label for="province" class="ml-label">Provinsi <span class="ml-required">*</span></label>
                            <input id="province" name="province" type="text" class="ml-input @error('province') is-invalid @enderror" value="{{ old('province', $facility->province) }}" required autocomplete="address-level1" data-error-required="Provinsi wajib diisi.">
                            <x-form.error for="province" />
                        </div>

                        <div class="ml-field">
                            <label for="city" class="ml-label">Kota/kabupaten <span class="ml-required">*</span></label>
                            <input id="city" name="city" type="text" class="ml-input @error('city') is-invalid @enderror" value="{{ old('city', $facility->city) }}" required autocomplete="address-level2" data-error-required="Kota/kabupaten wajib diisi.">
                            <x-form.error for="city" />
                        </div>

                        <div class="ml-field">
                            <label for="district" class="ml-label">Kecamatan</label>
                            <input id="district" name="district" type="text" class="ml-input @error('district') is-invalid @enderror" value="{{ old('district', $facility->district) }}" autocomplete="address-level3">
                            <x-form.error for="district" />
                        </div>

                        <div class="ml-field">
                            <label for="contact_person" class="ml-label">Penanggung jawab <span class="ml-required">*</span></label>
                            <input id="contact_person" name="contact_person" type="text" class="ml-input @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $facility->contact_person) }}" required autocomplete="name" data-error-required="Nama penanggung jawab wajib diisi.">
                            <x-form.error for="contact_person" />
                        </div>

                        <div class="ml-field">
                            <label for="phone" class="ml-label">Telepon <span class="ml-required">*</span></label>
                            <input id="phone" name="phone" type="tel" class="ml-input @error('phone') is-invalid @enderror" value="{{ old('phone', $facility->phone) }}" required autocomplete="tel" inputmode="tel" data-error-required="Nomor telepon wajib diisi.">
                            <x-form.error for="phone" />
                        </div>

                        <div class="ml-field is-full">
                            <label for="email" class="ml-label">Email fasilitas</label>
                            <input id="email" name="email" type="email" class="ml-input @error('email') is-invalid @enderror" value="{{ old('email', $facility->email) }}" autocomplete="email" inputmode="email" data-error-email="Format email belum benar, contoh: nama@faskes.id.">
                            <x-form.error for="email" />
                        </div>

                        <div class="ml-field is-full">
                            <label class="ml-checkbox-row">
                                <input type="checkbox" name="cold_chain_ready" value="1" @checked(old('cold_chain_ready', $facility->cold_chain_ready))>
                                <span>Fasilitas memiliki rantai dingin (2–8°C).</span>
                            </label>
                        </div>
                    </div>

                    <div class="ml-action-row">
                        <a href="{{ route('dashboard') }}" class="ml-btn-quiet">Batal</a>
                        <button type="submit" class="ml-btn-primary">Simpan perubahan</button>
                    </div>
                </div>
            </section>
        </form>
    </div>
</div>
@endsection
