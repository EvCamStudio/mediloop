@extends('layouts.mediloop', ['title' => 'Edit Fasilitas'])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Administrasi</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('admin.facilities.index') }}">Fasilitas</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('admin.facilities.show', $facility) }}">{{ $facility->name }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Edit</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">Edit fasilitas</h1>
                @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
            </div>
            <p class="ml-page-sub">Perubahan data identitas atau lokasi tidak mengubah status verifikasi secara otomatis.</p>
        </div>
    </header>

    @include('admin.facilities.partials.form', [
        'facility' => $facility,
        'action' => route('admin.facilities.update', $facility),
        'submitLabel' => 'Simpan perubahan',
        'cancelUrl' => route('admin.facilities.show', $facility),
    ])
</div>
@endsection
