<?php

namespace App\Events;

use App\Models\TeamMember;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamMemberInvited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TeamMember $teamMember;
    public string $temporaryPassword;

    public function __construct(TeamMember $teamMember, string $temporaryPassword)
    {
        $this->teamMember = $teamMember;
        $this->temporaryPassword = $temporaryPassword;
    }
}