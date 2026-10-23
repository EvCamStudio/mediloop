@extends('layouts.mediloop', ['title' => 'Pengguna'])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Administrasi</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Pengguna</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">Akun pengguna</h1>
            <p class="ml-page-sub">Akun hanya dibuat melalui undangan fasilitas yang sudah terverifikasi.</p>
        </div>
    </header>

    <section class="ml-section">
        <form method="GET" action="{{ route('admin.users.index') }}" class="ml-toolbar" aria-label="Filter pengguna">
            <div style="flex: 1; min-width: 220px;">
                <label for="search" class="ml-label">Cari</label>
                <input id="search" name="search" type="search" class="ml-input" value="{{ $search }}" placeholder="Nama atau email">
            </div>
            <div style="width: 190px;">
                <label for="role" class="ml-label">Peran</label>
                <select id="role" name="role" class="ml-select">
                    <option value="">Semua peran</option>
                    @foreach(\App\Enums\UserRole::cases() as $roleOption)
                        <option value="{{ $roleOption->value }}" @selected($role === $roleOption->value)>{{ $roleOption->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 170px;">
                <label for="status" class="ml-label">Status</label>
                <select id="status" name="status" class="ml-select">
                    <option value="">Semua status</option>
                    @foreach(\App\Enums\UserStatus::cases() as $statusOption)
                        <option value="{{ $statusOption->value }}" @selected($status === $statusOption->value)>{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 8px; align-self: flex-end;">
                <button type="submit" class="ml-btn-primary">Terapkan</button>
                @if($search !== '' || $role !== '' || $status !== '')
                    <a href="{{ route('admin.users.index') }}" class="ml-btn-quiet">Reset</a>
                @endif
            </div>
        </form>

        @if($users->isEmpty())
            <div class="ml-empty-state">
                <p class="ml-empty-title">Tidak ada pengguna</p>
                <p class="ml-empty-copy">Ubah filter atau kirim undangan dari detail fasilitas.</p>
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy">
                    <caption class="ml-sr-only">Daftar akun pengguna MediLoop</caption>
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Peran</th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Status</th>
                            <th scope="col">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $account)
                            <tr>
                                <td>
                                    <div class="ml-cell-title">{{ $account->name }}</div>
                                    <div class="ml-cell-meta">{{ $account->email }}</div>
                                </td>
                                <td>{{ $account->role_label }}</td>
                                <td>
                                    @if($account->facility)
                                        <a href="{{ route('admin.facilities.show', $account->facility) }}" class="ml-link-action">{{ $account->facility->name }}</a>
                                    @else
                                        <span class="ml-cell-meta">Platform</span>
                                    @endif
                                </td>
                                <td>@include('admin.partials.status', ['status' => $account->status, 'label' => \App\Enums\UserStatus::tryFrom($account->status)?->label() ?? $account->status])</td>
                                <td class="ml-cell-meta">{{ ($account->joined_at ?? $account->created_at)?->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px 20px;">
                {{ $users->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
