@extends('layouts.mediloop', ['title' => 'Tambah Fasilitas'])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Administrasi</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('admin.facilities.index') }}">Fasilitas</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Tambah</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">Tambah fasilitas</h1>
            <p class="ml-page-sub">Fasilitas baru disimpan sebagai menunggu verifikasi. Verifikasi untuk mengaktifkan dan mengundang tim.</p>
        </div>
    </header>

    @include('admin.facilities.partials.form', [
        'facility' => $facility,
        'action' => route('admin.facilities.store'),
        'submitLabel' => 'Simpan fasilitas',
        'cancelUrl' => route('admin.facilities.index'),
    ])
</div>
@endsection
