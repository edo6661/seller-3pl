<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $status = $request->get('status');
        
        $users = $this->userService->getAllUsers($search, $role, $status);
        $stats = $this->userService->getUserStats();
        
        return view('admin.user.index', compact('users', 'stats', 'search', 'role', 'status'));
    }
    public function approve(User $user)
    {
        $this->userService->approveVerification($user);

        return redirect()->route('admin.users.index')->with('success', 'Verifikasi seller berhasil disetujui.');
    }

    /**
     * Menolak verifikasi seller.
     */
    public function reject(Request $request, User $user)
    {
        $request->validate(['notes' => 'required|string|max:500']);

        $this->userService->rejectVerification($user, $request->input('notes'));

        return redirect()->route('admin.users.index')->with('success', 'Verifikasi seller berhasil ditolak.');
    }
}