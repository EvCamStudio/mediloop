@component('mail::message')
# Undangan MediLoop

Anda diundang untuk bergabung dengan **{{ $facilityName }}** sebagai **{{ $roleLabel }}**.

@component('mail::button', ['url' => $url])
Terima undangan
@endcomponent

Tautan ini berlaku sampai **{{ $expiresAt->format('d M Y H:i') }}** dan hanya dapat digunakan satu kali.

Jika Anda tidak merasa diundang, abaikan email ini.

Terima kasih,<br>
MediLoop
@endcomponent
