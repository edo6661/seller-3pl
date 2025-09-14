<?php

namespace App\Services;

use App\Events\TeamMemberInvited;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamService
{
    public function getTeamMembers(int $sellerId, ?string $search = null): LengthAwarePaginator
    {
        $query = TeamMember::where('seller_id', $sellerId)
            ->with(['user']);
            
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function inviteTeamMember(int $sellerId, array $data): TeamMember
    {
        return DB::transaction(function () use ($sellerId, $data) {
            // Generate temporary password
            $tempPassword = Str::random(8);
            
            $teamMember = TeamMember::create([
                'user_id' => null,
                'seller_id' => $sellerId,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($tempPassword),
                'permissions' => $data['permissions'] ?? [],
                'invited_at' => now(),
            ]);

            // Kirim email invitation
            event(new TeamMemberInvited($teamMember, $tempPassword));

            return $teamMember;
        });
    }
    public function acceptInvitation(string $email, string $tempPassword, string $newPassword): bool
    {
        $teamMember = TeamMember::where('email', $email)
            ->whereNull('accepted_at')
            ->first();
            
        if (!$teamMember || !Hash::check($tempPassword, $teamMember->password)) {
            return false;
        }
        
        // Create user account
        $user = User::create([
            'name' => $teamMember->name,
            'email' => $teamMember->email,
            'password' => Hash::make($newPassword),
            'phone' => $teamMember->phone,
            'role' => 'seller', // Role sebagai seller tapi dengan akses terbatas
            'email_verified_at' => now(),
        ]);
        
        // Update team member
        $teamMember->update([
            'user_id' => $user->id,
            'password' => Hash::make($newPassword),
            'accepted_at' => now(),
        ]);
        
        return true;
    }

    public function updateTeamMember(int $teamMemberId, array $data): TeamMember
    {
        $teamMember = TeamMember::findOrFail($teamMemberId);
        
        $updateData = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'permissions' => $data['permissions'] ?? [],
            'is_active' => $data['is_active'] ?? true,
            'user_id' => $data['user_id'] ?? null,
        ];

        $teamMember->update($updateData);
        
        return $teamMember->fresh();
    }

    public function removeTeamMember(int $teamMemberId): bool
    {
        return TeamMember::findOrFail($teamMemberId)->delete();
    }

    public function toggleTeamMemberStatus(int $teamMemberId): TeamMember
    {
        $teamMember = TeamMember::findOrFail($teamMemberId);
        $teamMember->update(['is_active' => !$teamMember->is_active]);
        
        return $teamMember->fresh();
    }

    public function getTeamStats(int $sellerId): array
    {
        $total = TeamMember::where('seller_id', $sellerId)->count();
        $active = TeamMember::where('seller_id', $sellerId)->active()->count();
        $pending = TeamMember::where('seller_id', $sellerId)->pendingInvitation()->count();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'pending_invitations' => $pending,
        ];
    }
}