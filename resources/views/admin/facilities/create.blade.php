@extends('layouts.mediloop', ['title' => __('admin.facility_form.create_title')])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('admin.breadcrumb') }}">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin.administration') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('admin.facilities.index') }}">{{ __('admin.facilities') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('admin.facility_form.create_breadcrumb') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">{{ __('admin.facility_form.create_heading') }}</h1>
            <p class="ml-page-sub">{{ __('admin.facility_form.create_subtitle') }}</p>
        </div>
    </header>

    @include('admin.facilities.partials.form', [
        'facility' => $facility,
        'action' => route('admin.facilities.store'),
        'submitLabel' => __('admin.facility_form.save_facility'),
        'cancelUrl' => route('admin.facilities.index'),
    ])
</div>
@endsection
