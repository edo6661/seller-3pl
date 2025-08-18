<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sellerId = auth()->id();
        
        $teamMembers = $this->teamService->getTeamMembers($sellerId, $search);
        $stats = $this->teamService->getTeamStats($sellerId);
        $availablePermissions = TeamMember::getAvailablePermissions();
        
        return view('seller.team.index', compact('teamMembers', 'stats', 'search', 'availablePermissions'));
    }

    public function create()
    {
        $availablePermissions = TeamMember::getAvailablePermissions();
        return view('seller.team.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:team_members,email|unique:users,email',
            'phone' => 'nullable|string|max:15',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|in:' . implode(',', array_keys(TeamMember::getAvailablePermissions())),
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah digunakan',
            'permissions.required' => 'Minimal pilih 1 hak akses',
            'permissions.min' => 'Minimal pilih 1 hak akses',
        ]);

        try {
            $this->teamService->inviteTeamMember(auth()->id(), $request->all());
            
            return redirect()
                ->route('seller.team.index')
                ->with('success', 'Anggota tim berhasil diundang!');
                
        } catch (\Exception $e) {
            Log::error('Error inviting team member: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengundang anggota tim. Silakan coba lagi.');
        }
    }

    public function edit(int $id)
    {
        $teamMember = TeamMember::where('seller_id', auth()->id())->findOrFail($id);
        $availablePermissions = TeamMember::getAvailablePermissions();
        
        return view('seller.team.edit', compact('teamMember', 'availablePermissions'));
    }

    public function update(Request $request, int $id)
    {
        $teamMember = TeamMember::where('seller_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|in:' . implode(',', array_keys(TeamMember::getAvailablePermissions())),
            'is_active' => 'boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        try {
            $this->teamService->updateTeamMember($id, $request->all());
            
            return redirect()
                ->route('seller.team.index')
                ->with('success', 'Anggota tim berhasil diperbarui!');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui anggota tim.');
        }
    }

    public function destroy(int $id)
    {
        try {
            $teamMember = TeamMember::where('seller_id', auth()->id())->findOrFail($id);
            $this->teamService->removeTeamMember($id);
            
            return redirect()
                ->route('seller.team.index')
                ->with('success', 'Anggota tim berhasil dihapus!');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus anggota tim.');
        }
    }

    public function toggleStatus(int $id)
    {
        try {
            $teamMember = TeamMember::where('seller_id', auth()->id())->findOrFail($id);
            $updatedMember = $this->teamService->toggleTeamMemberStatus($id);
            
            $status = $updatedMember->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()
                ->route('seller.team.index')
                ->with('success', "Anggota tim berhasil {$status}!");
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status anggota tim.');
        }
    }
}