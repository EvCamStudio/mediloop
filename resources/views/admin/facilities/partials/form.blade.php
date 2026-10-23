@php
    $isEdit = $facility->exists;
    $types = \App\Enums\FacilityType::cases();
@endphp

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

<form method="POST" action="{{ $action }}" novalidate>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="ml-two-col">
        <div>
            <section class="ml-card" aria-labelledby="facility-identity-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="facility-identity-title">Identitas fasilitas</h2>
                    <p class="ml-card-sub">Data ini menjadi dasar verifikasi dan tidak dapat diubah sendiri oleh fasilitas.</p>
                </div>
                <div class="ml-card-body">
                    <div class="ml-form-grid">
                        <div class="ml-field is-full">
                            <label for="name" class="ml-label">Nama fasilitas <span class="ml-required">*</span></label>
                            <input id="name" name="name" type="text" class="ml-input @error('name') is-invalid @enderror" value="{{ old('name', $facility->name) }}" required>
                            @error('name')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field">
                            <label for="code" class="ml-label">Kode fasilitas <span class="ml-required">*</span></label>
                            <input id="code" name="code" type="text" class="ml-input @error('code') is-invalid @enderror" value="{{ old('code', $facility->code) }}" required>
                            @error('code')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field">
                            <label for="type" class="ml-label">Jenis <span class="ml-required">*</span></label>
                            <select id="type" name="type" class="ml-select @error('type') is-invalid @enderror" required>
                                <option value="">Pilih jenis</option>
                                @foreach($types as $facilityType)
                                    @if($facilityType !== \App\Enums\FacilityType::Dinkes)
                                        <option value="{{ $facilityType->value }}" @selected(old('type', $facility->type) === $facilityType->value)>{{ $facilityType->label() }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('type')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field">
                            <label for="ownership" class="ml-label">Kepemilikan</label>
                            <select id="ownership" name="ownership" class="ml-select">
                                <option value="">Pilih kepemilikan</option>
                                <option value="PEMERINTAH" @selected(old('ownership', $facility->ownership) === 'PEMERINTAH')>Pemerintah</option>
                                <option value="SWASTA" @selected(old('ownership', $facility->ownership) === 'SWASTA')>Swasta</option>
                                <option value="LAINNYA" @selected(old('ownership', $facility->ownership) === 'LAINNYA')>Lainnya</option>
                            </select>
                        </div>

                        <div class="ml-field is-full">
                            <label for="license_number" class="ml-label">Nomor izin</label>
                            <input id="license_number" name="license_number" type="text" class="ml-input" value="{{ old('license_number', $facility->license_number) }}">
                        </div>
                    </div>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="facility-contact-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="facility-contact-title">Lokasi &amp; kontak</h2>
                    <p class="ml-card-sub">Alamat resmi dan kanal komunikasi yang dapat diverifikasi.</p>
                </div>
                <div class="ml-card-body">
                    <div class="ml-form-grid">
                        <div class="ml-field is-full">
                            <label for="address" class="ml-label">Alamat <span class="ml-required">*</span></label>
                            <input id="address" name="address" type="text" class="ml-input @error('address') is-invalid @enderror" value="{{ old('address', $facility->address) }}" required>
                            @error('address')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field">
                            <label for="province" class="ml-label">Provinsi <span class="ml-required">*</span></label>
                            <input id="province" name="province" type="text" class="ml-input @error('province') is-invalid @enderror" value="{{ old('province', $facility->province) }}" required>
                            @error('province')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field">
                            <label for="city" class="ml-label">Kota/kabupaten <span class="ml-required">*</span></label>
                            <input id="city" name="city" type="text" class="ml-input @error('city') is-invalid @enderror" value="{{ old('city', $facility->city) }}" required>
                            @error('city')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field">
                            <label for="district" class="ml-label">Kecamatan</label>
                            <input id="district" name="district" type="text" class="ml-input" value="{{ old('district', $facility->district) }}">
                        </div>

                        <div class="ml-field">
                            <label for="contact_person" class="ml-label">Penanggung jawab <span class="ml-required">*</span></label>
                            <input id="contact_person" name="contact_person" type="text" class="ml-input @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $facility->contact_person) }}" required>
                            @error('contact_person')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field">
                            <label for="phone" class="ml-label">Telepon <span class="ml-required">*</span></label>
                            <input id="phone" name="phone" type="text" class="ml-input @error('phone') is-invalid @enderror" value="{{ old('phone', $facility->phone) }}" required>
                            @error('phone')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field">
                            <label for="email" class="ml-label">Email fasilitas</label>
                            <input id="email" name="email" type="email" class="ml-input @error('email') is-invalid @enderror" value="{{ old('email', $facility->email) }}">
                            @error('email')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="ml-field is-full">
                            <label class="ml-checkbox-row">
                                <input type="checkbox" name="cold_chain_ready" value="1" @checked(old('cold_chain_ready', $facility->cold_chain_ready))>
                                <span>Fasilitas memiliki rantai dingin (2–8°C).</span>
                            </label>
                        </div>

                        <div class="ml-field is-full">
                            <label for="notes" class="ml-label">Catatan verifikasi</label>
                            <textarea id="notes" name="notes" class="ml-textarea" placeholder="Catatan internal tentang dokumen atau hasil kunjungan.">{{ old('notes', $facility->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside>
            <section class="ml-card ml-sticky" aria-labelledby="facility-map-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="facility-map-title">Titik lokasi</h2>
                    <p class="ml-card-sub">Klik peta atau geser penanda. Koordinat dipakai untuk perhitungan jarak.</p>
                </div>
                <div class="ml-card-body">
                    <div id="facility-map" class="ml-map-picker" data-lat="{{ old('latitude', $facility->latitude ?? -6.1754) }}" data-lng="{{ old('longitude', $facility->longitude ?? 106.8272) }}" role="application" tabindex="0" aria-label="Peta pemilih lokasi fasilitas" aria-describedby="map-coordinates"></div>
                    <div class="ml-map-coordinates">
                        <span>Koordinat terpilih</span>
                        <code id="map-coordinates">{{ old('latitude', $facility->latitude ?? '-6.1754000') }}, {{ old('longitude', $facility->longitude ?? '106.8272000') }}</code>
                    </div>

                    <div class="ml-form-grid" style="margin-top: 16px;">
                        <div class="ml-field" style="margin-bottom: 0;">
                            <label for="latitude" class="ml-label">Latitude <span class="ml-required">*</span></label>
                            <input id="latitude" name="latitude" type="number" step="0.0000001" min="-90" max="90" class="ml-input @error('latitude') is-invalid @enderror" value="{{ old('latitude', $facility->latitude ?? -6.1754) }}" required>
                            @error('latitude')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>
                        <div class="ml-field" style="margin-bottom: 0;">
                            <label for="longitude" class="ml-label">Longitude <span class="ml-required">*</span></label>
                            <input id="longitude" name="longitude" type="number" step="0.0000001" min="-180" max="180" class="ml-input @error('longitude') is-invalid @enderror" value="{{ old('longitude', $facility->longitude ?? 106.8272) }}" required>
                            @error('longitude')<p class="ml-error-text">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </section>
        </aside>
    </div>

    <div class="ml-action-row" style="margin-top: 20px;">
        <a href="{{ $cancelUrl }}" class="ml-btn-quiet">Batal</a>
        <button type="submit" class="ml-btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

@vite('resources/js/facility-map-picker.js')
