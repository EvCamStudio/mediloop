@extends('layouts.mediloop', ['title' => __('admin.facility_form.edit_title')])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('admin.breadcrumb') }}">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin.administration') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('admin.facilities.index') }}">{{ __('admin.facilities') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('admin.facilities.show', $facility) }}">{{ $facility->name }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('admin.facility_form.edit_breadcrumb') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <div class="ml-page-title-row">
                <h1 class="ml-page-title">{{ __('admin.facility_form.edit_heading') }}</h1>
                @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
            </div>
            <p class="ml-page-sub">{{ __('admin.facility_form.edit_subtitle') }}</p>
        </div>
    </header>

    @include('admin.facilities.partials.form', [
        'facility' => $facility,
        'action' => route('admin.facilities.update', $facility),
        'submitLabel' => __('common.save_changes'),
        'cancelUrl' => route('admin.facilities.show', $facility),
    ])
</div>
@endsection
