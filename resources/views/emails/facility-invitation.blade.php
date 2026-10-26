@component('mail::message')
# {{ __('emails.invitation.heading') }}

{{ __('emails.invitation.intro', ['facility' => $facilityName, 'role' => $roleLabel]) }}

@component('mail::button', ['url' => $url])
{{ __('emails.invitation.accept') }}
@endcomponent

{{ __('emails.invitation.expiry', ['date' => $expiresAt->format('d M Y H:i')]) }}

{{ __('emails.invitation.ignore') }}

{{ __('emails.invitation.thanks') }}<br>
MediLoop
@endcomponent
