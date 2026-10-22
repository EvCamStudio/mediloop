<x-layouts::auth :title="__('Masuk')">
    @php
        $inputClass = 'w-full rounded-lg border border-[#DBE5F5] bg-white px-3.5 py-2.5 text-sm text-[#0B1F4B] shadow-xs outline-none transition placeholder:text-[#8A99B3] focus:border-[#1D4ED8] focus:ring-2 focus:ring-[#1D4ED8]/20';
    @endphp

    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0B1F4B]">Masuk ke MediLoop</h1>
            <p class="mt-1.5 text-sm text-[#52627F]">
                Gunakan akun fasilitas kesehatan Anda untuk melanjutkan.
            </p>
        </header>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm font-medium text-emerald-800"
                role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5" novalidate>
            @csrf

            <div>
                <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                    Email
                </label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    autocomplete="email" placeholder="nama@faskes.go.id" class="{{ $inputClass }}"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                    <p id="email-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between gap-3">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wide text-[#0B1F4B]">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-xs font-semibold text-[#1D4ED8] hover:text-[#1E40AF]">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    placeholder="••••••••" class="{{ $inputClass }}"
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')
                    <p id="password-error" class="mt-1.5 text-xs font-medium text-[#B42318]">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember" class="flex items-center gap-2.5 text-sm text-[#52627F]">
                <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))
                    class="size-4 rounded border-[#DBE5F5] text-[#1D4ED8] focus:ring-2 focus:ring-[#1D4ED8]/30">
                <span>Ingat saya di perangkat ini</span>
            </label>

            <button type="submit"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1D4ED8] px-4 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1D4ED8] focus-visible:ring-offset-2"
                data-test="login-button">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-[#52627F]">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-[#1D4ED8] hover:text-[#1E40AF]">Daftar akun faskes</a>
        </p>
    </div>
</x-layouts::auth>
