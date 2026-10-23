@extends('layouts.public', ['title' => 'Ajukan Fasilitas Kesehatan'])

@section('content')
@if($submitted)
    <div class="pub-success-shell">
        <div class="pub-success-card">
            <div class="pub-success-lead">
                <span class="pub-success-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </span>
                <p class="pub-eyebrow">Pengajuan diterima</p>
                <h2>Berkas fasilitas sedang ditinjau</h2>
                <p>
                    Administrator MediLoop akan memeriksa identitas, izin, dan lokasi fasilitas.
                    Bila disetujui, undangan akun dikirim ke email penanggung jawab.
                </p>
            </div>

            <div class="pub-success-next">
                <h3>Yang terjadi selanjutnya</h3>
                <ol class="pub-success-steps">
                    <li class="pub-success-step">
                        <span aria-hidden="true">01</span>
                        <span>
                            <strong>Verifikasi data</strong>
                            <small>Administrator mencocokkan data fasilitas dengan dokumen resmi.</small>
                        </span>
                    </li>
                    <li class="pub-success-step">
                        <span aria-hidden="true">02</span>
                        <span>
                            <strong>Undangan tim</strong>
                            <small>Undangan dibuat untuk kepala fasilitas atau staf yang kamu tunjuk.</small>
                        </span>
                    </li>
                    <li class="pub-success-step">
                        <span aria-hidden="true">03</span>
                        <span>
                            <strong>Akun aktif</strong>
                            <small>Akun baru dapat digunakan setelah undangan diterima.</small>
                        </span>
                    </li>
                </ol>

                <div class="pub-success-actions">
                    <a href="{{ route('home') }}" class="pub-btn">Kembali ke beranda</a>
                    <a href="{{ route('facility-requests.create') }}" class="pub-btn pub-btn-primary">Ajukan fasilitas lain</a>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="pub-onboarding">
        <aside class="pub-dossier" aria-labelledby="dossier-title">
            <p class="pub-eyebrow">Onboarding fasilitas</p>
            <h2 id="dossier-title">Berkas verifikasi</h2>
            <p>Lengkapi data resmi fasilitas. Setelah dikirim, administrator akan meninjau sebelum akun tim dibuat.</p>

            <ol class="pub-steps" aria-label="Tahapan onboarding">
                <li class="pub-step is-current">
                    <span class="pub-step-number" aria-hidden="true">01</span>
                    <span>
                        <span class="pub-step-title">Identitas &amp; legalitas</span>
                        <span class="pub-step-sub">Nama, jenis, kepemilikan, dan nomor izin.</span>
                    </span>
                </li>
                <li class="pub-step">
                    <span class="pub-step-number" aria-hidden="true">02</span>
                    <span>
                        <span class="pub-step-title">Lokasi fasilitas</span>
                        <span class="pub-step-sub">Alamat dan titik peta untuk perhitungan jarak.</span>
                    </span>
                </li>
                <li class="pub-step">
                    <span class="pub-step-number" aria-hidden="true">03</span>
                    <span>
                        <span class="pub-step-title">Kontak penanggung jawab</span>
                        <span class="pub-step-sub">Email tujuan undangan dan nomor yang dapat dihubungi.</span>
                    </span>
                </li>
                <li class="pub-step">
                    <span class="pub-step-number" aria-hidden="true">04</span>
                    <span>
                        <span class="pub-step-title">Verifikasi MediLoop</span>
                        <span class="pub-step-sub">Tinjauan manual sebelum akun tim diaktifkan.</span>
                    </span>
                </li>
            </ol>

            <div class="pub-dossier-divider"></div>

            <ul class="pub-trust-list">
                <li class="pub-trust-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
                        <path d="M9 12l2 2 4-4" />
                    </svg>
                    <span>Tanpa data pasien. Hanya identitas dan operasional fasilitas.</span>
                </li>
                <li class="pub-trust-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7v5l3 2" />
                    </svg>
                    <span>Verifikasi manual sebelum akun atau undangan dibuat.</span>
                </li>
                <li class="pub-trust-item">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13A4 4 0 0116 11" />
                    </svg>
                    <span>Akses tim diberikan lewat undangan, bukan pendaftaran terbuka.</span>
                </li>
            </ul>
        </aside>

        <div class="pub-form-panel">
            <header class="pub-form-intro">
                <p class="pub-eyebrow">Formulir pengajuan</p>
                <h1 class="pub-title">Ajukan fasilitas kesehatan Anda</h1>
                <p class="pub-sub">
                    Isi data resmi fasilitas. Pengajuan ini tidak membuat akun otomatis;
                    administrator akan memverifikasi sebelum undangan tim dikirim.
                </p>
                <div class="pub-form-meta" aria-label="Ringkasan formulir">
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                        </svg>
                        3 bagian
                    </span>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
                        </svg>
                        Tanpa data pasien
                    </span>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>
                        Verifikasi manual
                    </span>
                </div>
            </header>

            @if($errors->any())
                <div class="pub-alert pub-alert-error" role="alert" style="margin: 22px 32px 0;">
                    <strong>Periksa kembali isian berikut:</strong>
                    <ul>
                        @foreach($errors->keys() as $field)
                            <li><a href="#{{ $field }}">{{ $errors->first($field) }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('facility-requests.store') }}" novalidate>
                @csrf

                <section class="pub-section" id="identitas" aria-labelledby="identity-title">
                    <div class="pub-section-head">
                        <span class="pub-section-index" aria-hidden="true">01</span>
                        <div>
                            <h2 class="pub-section-title" id="identity-title">Identitas &amp; legalitas</h2>
                            <p class="pub-section-sub">Gunakan nama dan nomor izin sesuai dokumen resmi fasilitas.</p>
                        </div>
                    </div>

                    <div class="pub-grid">
                        <div class="pub-field is-full">
                            <label for="name" class="pub-label">Nama fasilitas <span class="pub-required">*</span></label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="pub-input @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                required
                                autocomplete="organization"
                                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                @if($errors->has('name')) aria-describedby="name-error" @endif
                            >
                            @error('name')<p class="pub-error" id="name-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="code" class="pub-label">Kode fasilitas <span class="pub-required">*</span></label>
                            <input
                                id="code"
                                name="code"
                                type="text"
                                class="pub-input @error('code') is-invalid @enderror"
                                value="{{ old('code') }}"
                                required
                                placeholder="Contoh: FSK-RSUD-TRK"
                                autocomplete="off"
                                aria-invalid="{{ $errors->has('code') ? 'true' : 'false' }}"
                                aria-describedby="code-help{{ $errors->has('code') ? ' code-error' : '' }}"
                            >
                            <p class="pub-help" id="code-help">Kode unik dari Dinkes atau internal fasilitas. Huruf, angka, strip, dan garis bawah.</p>
                            @error('code')<p class="pub-error" id="code-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="type" class="pub-label">Jenis fasilitas <span class="pub-required">*</span></label>
                            <select
                                id="type"
                                name="type"
                                class="pub-select @error('type') is-invalid @enderror"
                                required
                                aria-invalid="{{ $errors->has('type') ? 'true' : 'false' }}"
                                @if($errors->has('type')) aria-describedby="type-error" @endif
                            >
                                <option value="">Pilih jenis</option>
                                @foreach(\App\Enums\FacilityType::cases() as $facilityType)
                                    @if($facilityType !== \App\Enums\FacilityType::Dinkes)
                                        <option value="{{ $facilityType->value }}" @selected(old('type') === $facilityType->value)>{{ $facilityType->label() }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('type')<p class="pub-error" id="type-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="ownership" class="pub-label">Kepemilikan</label>
                            <select
                                id="ownership"
                                name="ownership"
                                class="pub-select @error('ownership') is-invalid @enderror"
                                aria-invalid="{{ $errors->has('ownership') ? 'true' : 'false' }}"
                                @if($errors->has('ownership')) aria-describedby="ownership-error" @endif
                            >
                                <option value="">Pilih kepemilikan</option>
                                <option value="PEMERINTAH" @selected(old('ownership') === 'PEMERINTAH')>Pemerintah</option>
                                <option value="SWASTA" @selected(old('ownership') === 'SWASTA')>Swasta</option>
                                <option value="LAINNYA" @selected(old('ownership') === 'LAINNYA')>Lainnya</option>
                            </select>
                            @error('ownership')<p class="pub-error" id="ownership-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="license_number" class="pub-label">Nomor izin fasilitas</label>
                            <input
                                id="license_number"
                                name="license_number"
                                type="text"
                                class="pub-input @error('license_number') is-invalid @enderror"
                                value="{{ old('license_number') }}"
                                placeholder="Contoh: 445/001/RSUD-DKI/2022"
                                aria-invalid="{{ $errors->has('license_number') ? 'true' : 'false' }}"
                                @if($errors->has('license_number')) aria-describedby="license-number-error" @endif
                            >
                            @error('license_number')<p class="pub-error" id="license-number-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="pub-section" id="lokasi" aria-labelledby="location-title">
                    <div class="pub-section-head">
                        <span class="pub-section-index" aria-hidden="true">02</span>
                        <div>
                            <h2 class="pub-section-title" id="location-title">Lokasi fasilitas</h2>
                            <p class="pub-section-sub">Titik lokasi dipakai untuk menghitung jarak antar fasilitas, bukan alamat pasien.</p>
                        </div>
                    </div>

                    <div class="pub-grid">
                        <div class="pub-field is-full">
                            <label for="address" class="pub-label">Alamat lengkap <span class="pub-required">*</span></label>
                            <input
                                id="address"
                                name="address"
                                type="text"
                                class="pub-input @error('address') is-invalid @enderror"
                                value="{{ old('address') }}"
                                required
                                autocomplete="street-address"
                                aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}"
                                @if($errors->has('address')) aria-describedby="address-error" @endif
                            >
                            @error('address')<p class="pub-error" id="address-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="province" class="pub-label">Provinsi <span class="pub-required">*</span></label>
                            <input
                                id="province"
                                name="province"
                                type="text"
                                class="pub-input @error('province') is-invalid @enderror"
                                value="{{ old('province') }}"
                                required
                                autocomplete="address-level1"
                                aria-invalid="{{ $errors->has('province') ? 'true' : 'false' }}"
                                @if($errors->has('province')) aria-describedby="province-error" @endif
                            >
                            @error('province')<p class="pub-error" id="province-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="city" class="pub-label">Kota/kabupaten <span class="pub-required">*</span></label>
                            <input
                                id="city"
                                name="city"
                                type="text"
                                class="pub-input @error('city') is-invalid @enderror"
                                value="{{ old('city') }}"
                                required
                                autocomplete="address-level2"
                                aria-invalid="{{ $errors->has('city') ? 'true' : 'false' }}"
                                @if($errors->has('city')) aria-describedby="city-error" @endif
                            >
                            @error('city')<p class="pub-error" id="city-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="district" class="pub-label">Kecamatan</label>
                            <input
                                id="district"
                                name="district"
                                type="text"
                                class="pub-input @error('district') is-invalid @enderror"
                                value="{{ old('district') }}"
                                autocomplete="address-level3"
                                aria-invalid="{{ $errors->has('district') ? 'true' : 'false' }}"
                                @if($errors->has('district')) aria-describedby="district-error" @endif
                            >
                            @error('district')<p class="pub-error" id="district-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field is-full">
                            <span class="pub-label">Titik lokasi di peta <span class="pub-required">*</span></span>
                            <div
                                id="facility-map"
                                class="pub-map"
                                data-lat="{{ old('latitude', -6.1754) }}"
                                data-lng="{{ old('longitude', 106.8272) }}"
                                role="application"
                                tabindex="0"
                                aria-label="Peta pemilih lokasi fasilitas"
                                aria-describedby="map-coordinates"
                            ></div>
                            <div class="pub-map-meta">
                                <span>Klik peta atau geser penanda untuk menetapkan lokasi.</span>
                                <code id="map-coordinates">{{ old('latitude', '-6.1754000') }}, {{ old('longitude', '106.8272000') }}</code>
                            </div>
                        </div>

                        <div class="pub-field">
                            <label for="latitude" class="pub-label">Latitude <span class="pub-required">*</span></label>
                            <input
                                id="latitude"
                                name="latitude"
                                type="number"
                                step="0.0000001"
                                min="-90"
                                max="90"
                                class="pub-input @error('latitude') is-invalid @enderror"
                                value="{{ old('latitude', -6.1754) }}"
                                required
                                aria-invalid="{{ $errors->has('latitude') ? 'true' : 'false' }}"
                                @if($errors->has('latitude')) aria-describedby="latitude-error" @endif
                            >
                            @error('latitude')<p class="pub-error" id="latitude-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="longitude" class="pub-label">Longitude <span class="pub-required">*</span></label>
                            <input
                                id="longitude"
                                name="longitude"
                                type="number"
                                step="0.0000001"
                                min="-180"
                                max="180"
                                class="pub-input @error('longitude') is-invalid @enderror"
                                value="{{ old('longitude', 106.8272) }}"
                                required
                                aria-invalid="{{ $errors->has('longitude') ? 'true' : 'false' }}"
                                @if($errors->has('longitude')) aria-describedby="longitude-error" @endif
                            >
                            @error('longitude')<p class="pub-error" id="longitude-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="pub-section" id="kontak" aria-labelledby="contact-title">
                    <div class="pub-section-head">
                        <span class="pub-section-index" aria-hidden="true">03</span>
                        <div>
                            <h2 class="pub-section-title" id="contact-title">Kontak penanggung jawab</h2>
                            <p class="pub-section-sub">Undangan akun akan dikirim ke email ini setelah fasilitas disetujui.</p>
                        </div>
                    </div>

                    <div class="pub-grid">
                        <div class="pub-field">
                            <label for="contact_person" class="pub-label">Nama penanggung jawab <span class="pub-required">*</span></label>
                            <input
                                id="contact_person"
                                name="contact_person"
                                type="text"
                                class="pub-input @error('contact_person') is-invalid @enderror"
                                value="{{ old('contact_person') }}"
                                required
                                autocomplete="name"
                                aria-invalid="{{ $errors->has('contact_person') ? 'true' : 'false' }}"
                                @if($errors->has('contact_person')) aria-describedby="contact-person-error" @endif
                            >
                            @error('contact_person')<p class="pub-error" id="contact-person-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field">
                            <label for="phone" class="pub-label">Telepon <span class="pub-required">*</span></label>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                class="pub-input @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}"
                                required
                                autocomplete="tel"
                                inputmode="tel"
                                aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                                @if($errors->has('phone')) aria-describedby="phone-error" @endif
                            >
                            @error('phone')<p class="pub-error" id="phone-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field is-full">
                            <label for="email" class="pub-label">Email resmi <span class="pub-required">*</span></label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                class="pub-input @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                inputmode="email"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                @if($errors->has('email')) aria-describedby="email-error" @endif
                            >
                            @error('email')<p class="pub-error" id="email-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="pub-field is-full">
                            <label class="pub-checkbox">
                                <input type="checkbox" name="cold_chain_ready" value="1" @checked(old('cold_chain_ready'))>
                                <span>Fasilitas memiliki fasilitas rantai dingin (2–8°C) untuk vaksin atau sediaan sensitif suhu.</span>
                            </label>
                        </div>

                        <div class="pub-field is-full">
                            <label for="notes" class="pub-label">Catatan tambahan</label>
                            <textarea
                                id="notes"
                                name="notes"
                                class="pub-textarea @error('notes') is-invalid @enderror"
                                placeholder="Informasi lain yang membantu verifikasi, misalnya jam operasional atau kontak alternatif."
                                aria-invalid="{{ $errors->has('notes') ? 'true' : 'false' }}"
                                @if($errors->has('notes')) aria-describedby="notes-error" @endif
                            >{{ old('notes') }}</textarea>
                            @error('notes')<p class="pub-error" id="notes-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <div class="pub-submitbar">
                    <p class="pub-submit-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v4m0 4h.01" />
                        </svg>
                        <span>Pengajuan tidak membuat akun otomatis. Akun hanya dibuat lewat undangan setelah fasilitas diverifikasi.</span>
                    </p>
                    <div class="pub-submit-actions">
                        <a href="{{ route('home') }}" class="pub-btn">Batal</a>
                        <button type="submit" class="pub-btn pub-btn-primary">Kirim pengajuan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@section('scripts')
    @vite('resources/js/facility-map-picker.js')
@endsection
