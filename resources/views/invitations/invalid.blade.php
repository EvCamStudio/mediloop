@extends('layouts.public', ['title' => 'Undangan Tidak Valid'])

@section('content')
<div class="pub-page-narrow" style="max-width: 560px;">
    <section class="pub-success">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="12" r="9" />
            <path d="M12 8v5m0 3h.01" />
        </svg>
        <h2>Undangan tidak dapat digunakan</h2>
        <p>{{ $message }}</p>
        <a href="{{ route('login') }}" class="pub-btn">Ke halaman masuk</a>
    </section>
</div>
@endsection
