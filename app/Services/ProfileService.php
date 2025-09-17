<?php
namespace App\Services;
use App\Enums\SellerVerificationStatus;
use App\Models\User;
use App\Models\SellerProfile;
use App\Enums\UserRole;
use App\Events\SellerDocumentsUploaded;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; 
class ProfileService
{
    /**
     * Get user profile data based on role
     */
    public function getUserProfile(int $userId): array
    {
        $user = User::with('sellerProfile')->findOrFail($userId);
        $profileData = [
            'user' => $user,
            'user_fields' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
            ]
        ];
        if ($user->isSeller()) {
            $sellerProfile = $user->sellerProfile;
            $profileData['seller_profile'] = $sellerProfile;
            $profileData['profile_fields'] = $sellerProfile ? [
                'address' => $sellerProfile->address,
                'city' => $sellerProfile->city,
                'province' => $sellerProfile->province,
                'postal_code' => $sellerProfile->postal_code,
                'latitude' => $sellerProfile->latitude,
                'longitude' => $sellerProfile->longitude,
            ] : [];
        }
        return $profileData;
    }
    /**
     * Update user profile based on role
     */
    public function updateProfile(int $userId, array $data): bool
    {
        try {
            Log::info('Updating profile for user ID: ' . $userId, ['data' => $data]);
            DB::beginTransaction();
            $user = User::findOrFail($userId);
            $userFields = [];
            if (isset($data['name'])) $userFields['name'] = $data['name'];
            if (isset($data['email'])) $userFields['email'] = $data['email'];
            if (isset($data['phone'])) $userFields['phone'] = $data['phone'];
            if (isset($data['avatar'])) $userFields['avatar'] = $data['avatar'];
            if (isset($data['password']) && !empty($data['password'])) {
                $userFields['password'] = Hash::make($data['password']);
            }
            if (!empty($userFields)) {
                $user->update($userFields);
            }
            if ($user->isSeller()) {
                $profile = $this->getOrCreateSellerProfile($userId);
                $oldStatus = $profile->verification_status;
                $profileFields = [];
                if (isset($data['address'])) $profileFields['address'] = $data['address'];
                if (isset($data['city'])) $profileFields['city'] = $data['city'];
                if (isset($data['province'])) $profileFields['province'] = $data['province'];
                if (isset($data['postal_code'])) $profileFields['postal_code'] = $data['postal_code'];
                if (isset($data['latitude'])) $profileFields['latitude'] = $data['latitude'];
                if (isset($data['longitude'])) $profileFields['longitude'] = $data['longitude'];
                $uploadedDocuments = [];
                if (isset($data['ktp_image'])) {
                    $profileFields['ktp_image_path'] = $this->uploadVerificationDocument($profile, $data['ktp_image'], 'ktp');
                    $uploadedDocuments[] = 'ktp';
                }
                if (isset($data['passbook_image'])) {
                    $profileFields['passbook_image_path'] = $this->uploadVerificationDocument($profile, $data['passbook_image'], 'passbook');
                    $uploadedDocuments[] = 'passbook';
                }
                if (!empty($uploadedDocuments)) {
                    $profileFields['verification_status'] = SellerVerificationStatus::PENDING;
                    $profileFields['verification_notes'] = null;
                }
                if (!empty($profileFields)) {
                    $profile->update($profileFields);
                }
                if (!empty($uploadedDocuments)) {
                    event(new SellerDocumentsUploaded(
                        $user,
                        $uploadedDocuments,
                        false 
                    ));
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    /**
     * Get or create seller profile
     */
    public function getOrCreateSellerProfile(int $userId): ?SellerProfile
    {
        $user = User::findOrFail($userId);
        if (!$user->isSeller()) {
            return null;
        }
        $profile = SellerProfile::where('user_id', $userId)->first();
        if (!$profile) {
            $profile = SellerProfile::create([
                'user_id' => $userId,
                'address' => '',
                'city' => '',
                'province' => '',
                'postal_code' => '',
            ]);
        }
        return $profile;
    }
    /**
     * Update or create seller profile
     */
    public function updateOrCreateSellerProfile(int $userId, array $data): SellerProfile
    {
        return SellerProfile::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }
    /**
     * Check if user has seller profile
     */
    public function hasSellerProfile(int $userId): bool
    {
        return SellerProfile::where('user_id', $userId)->exists();
    }
    /**
     * Check if seller profile is complete
     */
    public function isSellerProfileComplete(int $userId): bool
    {
        $profile = SellerProfile::where('user_id', $userId)->first();
        return $profile ? $profile->is_profile_complete : false;
    }
    /**
     * Update coordinates for seller profile
     */
    public function updateCoordinates(int $userId, float $latitude, float $longitude): SellerProfile
    {
        $profile = SellerProfile::where('user_id', $userId)->firstOrFail();
        $profile->update([
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
        return $profile;
    }
    /**
     * Upload avatar
     */
    public function uploadAvatar(int $userId, $avatarFile): string
    {
        $user = User::findOrFail($userId);
        if ($user->avatar) {
            Storage::disk('r2')->delete($user->avatar);
        }
        $avatarPath = $avatarFile->store('avatars', 'r2');
        $user->update(['avatar' => $avatarPath]);
        return $avatarPath;
    }
    /**
     * Get profile completion percentage
     */
    public function getProfileCompletionPercentage(int $userId): int
    {
        $user = User::with('sellerProfile')->findOrFail($userId);
        $totalFields = $user->isSeller() ? 8 : 4; 
        $completedFields = 0;
        if (!empty($user->name)) $completedFields++;
        if (!empty($user->email)) $completedFields++;
        if (!empty($user->phone)) $completedFields++;
        if (!empty($user->avatar)) $completedFields++;
        if ($user->isSeller() && $user->sellerProfile) {
            $profile = $user->sellerProfile;
            if (!empty($profile->address)) $completedFields++;
            if (!empty($profile->city)) $completedFields++;
            if (!empty($profile->province)) $completedFields++;
        if (!empty($profile->postal_code)) $completedFields++;
        }
        return round(($completedFields / $totalFields) * 100);
    }
        public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        try {
            $user = User::findOrFail($userId);
            if (!Hash::check($currentPassword, $user->password)) {
                throw new \Exception('Password saat ini tidak sesuai.');
            }
            $user->update([
                'password' => Hash::make($newPassword)
            ]);
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * Validate password strength
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf besar.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf kecil.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 angka.';
        }
        return $errors;
    }
     private function uploadVerificationDocument(SellerProfile $profile, UploadedFile $file, string $type): string
        {
            $user = $profile->user;
            $pathAttribute = $type . '_image_path';
            if ($profile->{$pathAttribute} && Storage::disk('r2')->exists($profile->{$pathAttribute})) {
                Storage::disk('r2')->delete($profile->{$pathAttribute});
            }
            $filePath = $file->store('seller_documents/' . $user->id, 'r2');
            return $filePath;
        }
        public function resubmitDocuments(int $userId, array $data): bool
        {
            $user = User::findOrFail($userId);
            $profile = $user->sellerProfile;
            if (!$profile) {
                return false;
            }
            $profileFields = [];
            $uploadedDocuments = [];
            if (isset($data['ktp_image'])) {
                $profileFields['ktp_image_path'] = $this->uploadVerificationDocument($profile, $data['ktp_image'], 'ktp');
                $uploadedDocuments[] = 'ktp';
            }
            if (isset($data['passbook_image'])) {
                $profileFields['passbook_image_path'] = $this->uploadVerificationDocument($profile, $data['passbook_image'], 'passbook');
                $uploadedDocuments[] = 'passbook';
            }
            $profileFields['verification_status'] = SellerVerificationStatus::PENDING;
            $profileFields['verification_notes'] = null;
            $result = $profile->update($profileFields);
            if ($result && !empty($uploadedDocuments)) {
                event(new SellerDocumentsUploaded(
                    $user,
                    $uploadedDocuments,
                    true 
                ));
            }
            return $result;
        }
    }