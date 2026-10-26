<?php

namespace App\Mail;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\FacilityInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacilityInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FacilityInvitation $invitation,
        public string $plainToken,
        public Facility $facility,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.invitation.subject', ['facility' => $this->facility->name]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.facility-invitation',
            with: [
                'url' => route('invitations.show', ['token' => $this->plainToken]),
                'facilityName' => $this->facility->name,
                'roleLabel' => UserRole::tryFrom($this->invitation->role)?->label() ?? $this->invitation->role,
                'expiresAt' => $this->invitation->expires_at,
            ],
        );
    }
}
