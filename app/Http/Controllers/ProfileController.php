<?php
namespace App\Http\Controllers;
use App\Enums\SellerVerificationStatus;
use App\Http\Controllers\Controller;
use App\Requests\Profile\ChangePasswordRequest;
use App\Requests\Profile\ResubmitVerificationRequest as ProfileResubmitVerificationRequest;
use App\Requests\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $userId = Auth::id();
            $profileData = $this->profileService->getUserProfile($userId);
            $completionPercentage = $this->profileService->getProfileCompletionPercentage($userId);
            $isPasswordExists = Auth::user()->password !== null;
            return view('profile.index', compact('profileData', 'completionPercentage', 'isPasswordExists'));
        } catch (\Exception $e) {
            Log::error('Error loading profile: ' . $e->getMessage(), [
                'user_id' => Auth::id()
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
            $userId = Auth::id();
            $profileData = $this->profileService->getUserProfile($userId);
            if (Auth::user()->isSeller()) {
                $this->profileService->getOrCreateSellerProfile($userId);
                $profileData = $this->profileService->getUserProfile($userId);
            }
            return view('profile.edit', compact('profileData'));
        } catch (\Exception $e) {
            Log::error('Error loading profile edit form: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat form edit profil.');
        }
    }
    /**
     * Update the user's profile
     */
    /**
 * @param \Illuminate\Http\Request $request
 */
    public function update(UpdateProfileRequest $request)
    {
        try {
            $userId = Auth::id();
            $data = $request->validated();
            if ($request->hasFile('avatar')) {
                $avatarPath = $this->profileService->uploadAvatar($userId, $request->file('avatar'));
                $data['avatar'] = $avatarPath;
            }
            if ($request->hasFile('ktp_image')) {
                $data['ktp_image'] = $request->file('ktp_image');
            }
            if ($request->hasFile('passbook_image')) {
                $data['passbook_image'] = $request->file('passbook_image');
            }
            $this->profileService->updateProfile($userId, $data);
            return redirect()
                ->route('profile.index')
                ->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
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
        if (!Auth::user()->isSeller()) {
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
                Auth::id(),
                $request->latitude,
                $request->longitude
            );
            return response()->json([
                'success' => true,
                'message' => 'Koordinat berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating coordinates: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
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
            $userId = Auth::id();
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
                'user_id' => Auth::id()
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
            $userId = Auth::id();
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
                'user_id' => Auth::id()
            ]);
            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: 'Gagal mengubah password. Silakan coba lagi.');
        }
    }
    public function resubmitVerificationForm()
    {
        $user = Auth::user();
        if (!$user->isSeller() || $user->sellerProfile->verification_status !== SellerVerificationStatus::REJECTED) {
            abort(403, 'Akses ditolak.');
        }
        $profileData = $this->profileService->getUserProfile($user->id);
        return view('profile.resubmit_verification', compact('profileData'));
    }
    /**
     * Memproses pengajuan ulang verifikasi.
     */
    public function processResubmission(ProfileResubmitVerificationRequest $request)
    {
        $this->profileService->resubmitDocuments(Auth::id(), $request->validated());
        return redirect()->route('profile.index')->with('success', 'Dokumen berhasil diajukan ulang dan akan segera ditinjau oleh admin.');
    }
}