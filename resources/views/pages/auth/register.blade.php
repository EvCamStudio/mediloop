<x-layouts::auth :title="__('Daftar Akun Faskes')">
    @php
        $inputClass = 'w-full rounded-lg border border-[#DBE5F5] bg-white px-3.5 py-2.5 text-sm text-[#0B1F4B] shadow-xs outline-none transition placeholder:text-[#8A99B3] focus:border-[#1D4ED8] focus:ring-2 focus:ring-[#1D4ED8]/20';
        $facilityOptions = $facilities ?? collect();
    @endphp

    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0B1F4B]">Daftar Akun Faskes</h1>
            <p class="mt-1.5 text-sm text-[#52627F]">
                Buat akun staf farmasi untuk fasilitas kesehatan yang sudah terdaftar.
            </p>
        </header>

        @if ($facilityOptions->isEmpty())
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-3.5 py-3 text-sm text-amber-900" role="alert">
                Belum ada fasilitas kesehatan terdaftar. Hubungi administrator platform sebelum membuat akun.
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5" novalidate>
            @csrf

            <div>
                <label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    Nama Lengkap
                </label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    autocomplete="name" placeholder="Apt. Nama Lengkap, S.Farm" class="{{ $inputClass }}"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')
                    <p id="name-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    Email
                </label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                    placeholder="nama@faskes.go.id" class="{{ $inputClass }}"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                    <p id="email-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="facility_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    Fasilitas Kesehatan
                </label>
                <select id="facility_id" name="facility_id" required class="{{ $inputClass }}"
                    @error('facility_id') aria-invalid="true" aria-describedby="facility-error" @enderror>
                    <option value="" disabled @selected(old('facility_id') === null)>Pilih fasilitas</option>
                    @foreach ($facilityOptions as $facility)
                        <option value="{{ $facility->id }}" @selected((string) old('facility_id') === (string) $facility->id)>
                            {{ $facility->name }}{{ filled($facility->city) ? ' — '.$facility->city : '' }}
                        </option>
                    @endforeach
                </select>
                @error('facility_id')
                    <p id="facility-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    Peran di Fasilitas
                </label>
                <select id="role" name="role" required class="{{ $inputClass }}"
                    @error('role') aria-invalid="true" aria-describedby="role-error" @enderror>
                    <option value="" disabled @selected(old('role') === null)>Pilih peran</option>
                    @foreach ($roles ?? [] as $value => $label)
                        <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role')
                    <p id="role-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="job_title" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                        Jabatan <span class="font-medium normal-case text-[#8A99B3]">(opsional)</span>
                    </label>
                    <input id="job_title" name="job_title" type="text" value="{{ old('job_title') }}"
                        autocomplete="organization-title" placeholder="Staf Farmasi" class="{{ $inputClass }}"
                        @error('job_title') aria-invalid="true" aria-describedby="job-title-error" @enderror>
                    @error('job_title')
                        <p id="job-title-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="license_sip" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                        No. SIP <span class="font-medium normal-case text-[#8A99B3]">(opsional)</span>
                    </label>
                    <input id="license_sip" name="license_sip" type="text" value="{{ old('license_sip') }}"
                        placeholder="SIPA.3171.01.2024.0001" class="{{ $inputClass }}"
                        @error('license_sip') aria-invalid="true" aria-describedby="license-error" @enderror>
                    @error('license_sip')
                        <p id="license-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    Password
                </label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    placeholder="Minimal 8 karakter" class="{{ $inputClass }}"
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')
                    <p id="password-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation"
                    class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    Konfirmasi Password
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password" placeholder="Ulangi password" class="{{ $inputClass }}">
            </div>

            <button type="submit"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1D4ED8] px-4 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1D4ED8] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                data-test="register-user-button" @disabled($facilityOptions->isEmpty())>
                Buat Akun
            </button>
        </form>

        <p class="text-center text-sm text-[#52627F]">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-[#1D4ED8] hover:text-[#1E40AF]">Masuk di sini</a>
        </p>
    </div>
</x-layouts::auth>
