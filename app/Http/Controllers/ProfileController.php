<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Requests\Profile\ChangePasswordRequest;
use App\Requests\Profile\UpdateProfileRequest as ProfileUpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Display the user's profile
     */
    public function index()
    {
        try {
            $userId = auth()->id();
            $profileData = $this->profileService->getUserProfile($userId);
            $completionPercentage = $this->profileService->getProfileCompletionPercentage($userId);
            $isPasswordExists = auth()->user()->password !== null;
            return view('profile.index', compact('profileData', 'completionPercentage', 'isPasswordExists'));
        } catch (\Exception $e) {
            Log::error('Error loading profile: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat profil.');
        }
    }

    /**
     * Show the form for editing the profile
     */
    public function edit()
    {
        try {
            $userId = auth()->id();
            $profileData = $this->profileService->getUserProfile($userId);
            
            
            if (auth()->user()->isSeller()) {
                $this->profileService->getOrCreateSellerProfile($userId);
            }
            
            return view('profile.edit', compact('profileData'));
        } catch (\Exception $e) {
            Log::error('Error loading profile edit form: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat form edit profil.');
        }
    }

    /**
     * Update the user's profile
     */
    public function update(ProfileUpdateProfileRequest $request)
    {
        try {
            $userId = auth()->id();
            $data = $request->validated();
            
            
            if ($request->hasFile('avatar')) {
                $avatarPath = $this->profileService->uploadAvatar($userId, $request->file('avatar'));
                $data['avatar'] = $avatarPath;
            }
            
            $this->profileService->updateProfile($userId, $data);
            
            return redirect()
                ->route('profile.index')
                ->with('success', 'Profil berhasil diperbarui!');
                
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    /**
     * Update coordinates for seller profile (AJAX)
     */
    public function updateCoordinates(Request $request)
    {
        if (!auth()->user()->isSeller()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya seller yang dapat mengupdate koordinat'
            ], 403);
        }
        
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        try {
            $this->profileService->updateCoordinates(
                auth()->id(),
                $request->latitude,
                $request->longitude
            );

            return response()->json([
                'success' => true,
                'message' => 'Koordinat berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating coordinates: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui koordinat'
            ], 500);
        }
    }

    /**
     * Get profile completion status (AJAX)
     */
    public function getCompletionStatus()
    {
        try {
            $userId = auth()->id();
            $percentage = $this->profileService->getProfileCompletionPercentage($userId);
            
            return response()->json([
                'success' => true,
                'completion_percentage' => $percentage,
                'is_complete' => $percentage >= 100
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status profil'
            ], 500);
        }
    }
    public function changePasswordForm()
    {
        try {
            return view('profile.change-password');
        } catch (\Exception $e) {
            Log::error('Error loading change password form: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat form ganti password.');
        }
    }

    /**
     * Change user password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $userId = auth()->id();
            $data = $request->validated();
            
            $this->profileService->changePassword(
                $userId,
                $data['current_password'],
                $data['password']
            );
            
            return redirect()
                ->route('profile.index')
                ->with('success', 'Password berhasil diubah!');
                
        } catch (\Exception $e) {
            Log::error('Error changing password: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);
            
            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: 'Gagal mengubah password. Silakan coba lagi.');
        }
    }

}