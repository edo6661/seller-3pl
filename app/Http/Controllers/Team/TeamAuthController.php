<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeamAuthController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function acceptInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'temp_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $accepted = $this->teamService->acceptInvitation(
                $request->email,
                $request->temp_password,
                $request->password
            );

            if ($accepted) {
                return redirect()
                    ->route('guest.auth.login')
                    ->with('success', 'Akun berhasil diaktivasi! Silakan login dengan password baru Anda.');
            }

            return back()
                ->withErrors(['temp_password' => 'Password sementara tidak valid atau undangan sudah kedaluwarsa.'])
                ->withInput();

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat mengaktivasi akun.')
                ->withInput();
        }
    }

    public function showAcceptForm(Request $request)
    {
        $email = $request->get('email');
        $token = $request->get('token');

        return view('team.accept-invitation', compact('email', 'token'));
    }
}