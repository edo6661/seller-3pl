<?php

namespace App\Mail;

use App\Models\TeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public TeamMember $teamMember;
    public string $temporaryPassword;
    public string $loginUrl;

    public function __construct(TeamMember $teamMember, string $temporaryPassword)
    {
        $this->teamMember = $teamMember;
        $this->temporaryPassword = $temporaryPassword;
        $this->loginUrl = route('guest.auth.login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Bergabung Tim - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-invitation',
            with: [
                'teamMember' => $this->teamMember,
                'sellerName' => $this->teamMember->seller->name,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => $this->loginUrl,
            ],
        );
    }
}