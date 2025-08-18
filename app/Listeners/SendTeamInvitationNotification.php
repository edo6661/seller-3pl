<?php

namespace App\Listeners;

use App\Events\TeamMemberInvited;
use App\Mail\TeamInvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTeamInvitationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TeamMemberInvited $event): void
    {
        Mail::to($event->teamMember->email)->send(
            new TeamInvitationMail($event->teamMember, $event->temporaryPassword)
        );
    }
}