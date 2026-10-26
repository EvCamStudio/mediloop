@extends('layouts.mediloop', ['title' => __('admin.users.title')])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('admin.breadcrumb') }}">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin.administration') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('admin.users.breadcrumb') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <h1 class="ml-page-title">{{ __('admin.users.heading') }}</h1>
            <p class="ml-page-sub">{{ __('admin.users.subtitle') }}</p>
        </div>
    </header>

    <section class="ml-section">
        <form method="GET" action="{{ route('admin.users.index') }}" class="ml-toolbar" aria-label="{{ __('admin.users.filter_aria') }}">
            <div style="flex: 1; min-width: 220px;">
                <label for="search" class="ml-label">{{ __('common.search') }}</label>
                <input id="search" name="search" type="search" class="ml-input" value="{{ $search }}" placeholder="{{ __('admin.users.search_placeholder') }}">
            </div>
            <div style="width: 190px;">
                <label for="role" class="ml-label">{{ __('common.role') }}</label>
                <select id="role" name="role" class="ml-select">
                    <option value="">{{ __('admin.users.all_roles') }}</option>
                    @foreach(\App\Enums\UserRole::cases() as $roleOption)
                        <option value="{{ $roleOption->value }}" @selected($role === $roleOption->value)>{{ $roleOption->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 170px;">
                <label for="status" class="ml-label">{{ __('common.status') }}</label>
                <select id="status" name="status" class="ml-select">
                    <option value="">{{ __('admin.users.all_statuses') }}</option>
                    @foreach(\App\Enums\UserStatus::cases() as $statusOption)
                        <option value="{{ $statusOption->value }}" @selected($status === $statusOption->value)>{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 8px; align-self: flex-end;">
                <button type="submit" class="ml-btn-primary">{{ __('common.apply') }}</button>
                @if($search !== '' || $role !== '' || $status !== '')
                    <a href="{{ route('admin.users.index') }}" class="ml-btn-quiet">{{ __('common.reset') }}</a>
                @endif
            </div>
        </form>

        @if($users->isEmpty())
            <div class="ml-empty-state">
                <p class="ml-empty-title">{{ __('admin.users.empty_title') }}</p>
                <p class="ml-empty-copy">{{ __('admin.users.empty_copy') }}</p>
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy">
                    <caption class="ml-sr-only">{{ __('admin.users.table_caption') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('common.name') }}</th>
                            <th scope="col">{{ __('common.role') }}</th>
                            <th scope="col">{{ __('admin.facility') }}</th>
                            <th scope="col">{{ __('common.status') }}</th>
                            <th scope="col">{{ __('admin.users.joined') }}</th>
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
                                        <span class="ml-cell-meta">{{ __('admin.users.platform') }}</span>
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
