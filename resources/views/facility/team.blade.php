@extends('layouts.mediloop', ['title' => __('facility.team.page_title')])

@section('content')
<div class="ml-page-admin">
    <header class="ml-passport">
        <div class="ml-passport-main">
            <span class="ml-passport-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($facility->name, 0, 1)) }}</span>
            <div class="ml-passport-copy">
                <p class="ml-passport-eyebrow">{{ __('facility.team.eyebrow') }}</p>
                <div class="ml-passport-title-row">
                    <h1 class="ml-page-title">{{ $facility->name }}</h1>
                    @include('admin.partials.status', ['status' => $facility->status, 'label' => $facility->status_label])
                </div>
                <p class="ml-passport-meta">{{ __('facility.team.subtitle') }}</p>
            </div>
        </div>
        <div class="ml-passport-actions">
            <a href="{{ route('facility.settings.edit') }}" class="ml-btn-quiet">{{ __('facility.team.profile_link') }}</a>
        </div>
    </header>

    @if(session('status'))
        <x-alert variant="success">{{ session('status') }}</x-alert>
    @endif

    <x-form.summary for="teamInviteForm" />

    @if(! $facility->isActive())
        <x-alert variant="info">
            {{ __('facility.team.inactive_notice') }}
        </x-alert>
    @endif

    <div class="ml-inline-metrics" aria-label="{{ __('facility.team.summary_aria') }}">
        <div class="ml-inline-metric">
            <span class="ml-inline-metric-value">{{ number_format($members->count(), 0, ',', '.') }}</span>
            <span class="ml-inline-metric-label">{{ __('facility.team.members_metric') }}</span>
        </div>
        <div class="ml-inline-metric">
            <span class="ml-inline-metric-value">{{ number_format($invitations->count(), 0, ',', '.') }}</span>
            <span class="ml-inline-metric-label">{{ __('facility.team.pending_invitations') }}</span>
        </div>
    </div>

    <div class="ml-two-col">
        <div>
            <section class="ml-card" aria-labelledby="members-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="members-title">{{ __('facility.team.members_title') }}</h2>
                    <p class="ml-card-sub">{{ __('facility.team.members_sub') }}</p>
                </div>
                @if($members->isEmpty())
                    <div class="ml-empty-state">
                        <p class="ml-empty-title">{{ __('facility.team.members_empty_title') }}</p>
                        <p class="ml-empty-copy">{{ __('facility.team.members_empty_copy') }}</p>
                    </div>
                @else
                    <div class="ml-table-responsive">
                        <table class="ml-table ml-table-airy ml-table-team">
                            <caption class="ml-sr-only">{{ __('facility.team.members_caption') }}</caption>
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('common.name') }}</th>
                                    <th scope="col">{{ __('common.role') }}</th>
                                    <th scope="col">{{ __('common.status') }}</th>
                                    <th scope="col">{{ __('facility.team.joined') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            <div class="ml-member-row">
                                                <span class="ml-member-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($member->name, 0, 1)) }}</span>
                                                <span class="ml-member-copy">
                                                    <span class="ml-cell-title">{{ $member->name }}</span>
                                                    <span class="ml-cell-meta">{{ $member->email }}</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td data-label="{{ __('common.role') }}">{{ $member->role_label }}</td>
                                        <td data-label="{{ __('common.status') }}">@include('admin.partials.status', ['status' => $member->status, 'label' => \App\Enums\UserStatus::tryFrom($member->status)?->label() ?? $member->status])</td>
                                        <td class="ml-cell-meta" data-label="{{ __('facility.team.joined') }}">{{ ($member->joined_at ?? $member->created_at)?->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="ml-card" aria-labelledby="invitations-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="invitations-title">{{ __('facility.team.pending_invitations') }}</h2>
                    <p class="ml-card-sub">{{ __('facility.team.invitations_sub') }}</p>
                </div>
                @if($invitations->isEmpty())
                    <div class="ml-card-body">
                        <p class="ml-help" style="margin: 0;">{{ __('facility.team.invitations_empty') }}</p>
                    </div>
                @else
                    <div class="ml-table-responsive">
                        <table class="ml-table ml-table-airy ml-table-team">
                            <caption class="ml-sr-only">{{ __('facility.team.invitations_caption') }}</caption>
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('common.email') }}</th>
                                    <th scope="col">{{ __('common.role') }}</th>
                                    <th scope="col">{{ __('facility.team.expires_at') }}</th>
                                    <th scope="col"><span class="ml-sr-only">{{ __('common.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invitations as $invitation)
                                    <tr>
                                        <td>{{ $invitation->email }}</td>
                                        <td data-label="{{ __('common.role') }}">{{ \App\Enums\UserRole::tryFrom($invitation->role)?->label() ?? $invitation->role }}</td>
                                        <td class="ml-cell-meta" data-label="{{ __('facility.team.expires_at') }}">{{ $invitation->expires_at->format('d M Y') }}</td>
                                        <td class="is-right">
                                            <form method="POST" action="{{ route('facility.team.invitations.destroy', $invitation) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-link-action ml-link-danger">{{ __('facility.team.revoke') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <aside>
            <section class="ml-card ml-sticky" aria-labelledby="invite-title">
                <div class="ml-card-head">
                    <h2 class="ml-card-title" id="invite-title">{{ __('facility.team.invite_title') }}</h2>
                    <p class="ml-card-sub">{{ __('facility.team.invite_sub') }}</p>
                </div>
                <div class="ml-card-body">
                    @if($facility->isActive())
                        <form method="POST" action="{{ route('facility.team.invite') }}" id="teamInviteForm" novalidate data-validate>
                            @csrf
                            <div class="ml-field">
                                <label for="email" class="ml-label">{{ __('common.email') }} <span class="ml-required">*</span></label>
                                <input id="email" name="email" type="email" class="ml-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="{{ __('facility.team.email_placeholder') }}" required data-error-required="{{ __('facility.team.email_required') }}" data-error-email="{{ __('facility.team.email_invalid') }}">
                                <x-form.error for="email" />
                            </div>

                            <div class="ml-field">
                                <label for="role" class="ml-label">{{ __('common.role') }} <span class="ml-required">*</span></label>
                                <select id="role" name="role" class="ml-select @error('role') is-invalid @enderror" required data-error-required="{{ __('facility.team.role_required') }}">
                                    @foreach($roles as $value => $label)
                                        <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-form.error for="role" />
                            </div>

                            <button type="submit" class="ml-btn-primary ml-btn-block">{{ __('facility.team.send_invitation') }}</button>
                        </form>
                    @else
                        <p class="ml-help" style="margin-top: 0;">{{ __('facility.team.invite_unavailable') }}</p>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
