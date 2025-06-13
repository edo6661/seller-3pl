<?php

namespace App\Services;

use App\Models\User;
use App\Models\SellerProfile;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        
        // If seller, include seller profile data
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
            DB::beginTransaction();
            
            $user = User::findOrFail($userId);
            
            // Extract user fields
            $userFields = [];
            if (isset($data['name'])) $userFields['name'] = $data['name'];
            if (isset($data['email'])) $userFields['email'] = $data['email'];
            if (isset($data['phone'])) $userFields['phone'] = $data['phone'];
            if (isset($data['avatar'])) $userFields['avatar'] = $data['avatar'];
            if (isset($data['password']) && !empty($data['password'])) {
                $userFields['password'] = Hash::make($data['password']);
            }
            
            // Update user fields
            if (!empty($userFields)) {
                $user->update($userFields);
            }
            
            // If seller, also update seller profile
            if ($user->isSeller()) {
                $profileFields = [];
                if (isset($data['address'])) $profileFields['address'] = $data['address'];
                if (isset($data['city'])) $profileFields['city'] = $data['city'];
                if (isset($data['province'])) $profileFields['province'] = $data['province'];
                if (isset($data['postal_code'])) $profileFields['postal_code'] = $data['postal_code'];
                if (isset($data['latitude'])) $profileFields['latitude'] = $data['latitude'];
                if (isset($data['longitude'])) $profileFields['longitude'] = $data['longitude'];
                
                if (!empty($profileFields)) {
                    $this->updateOrCreateSellerProfile($userId, $profileFields);
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
        
        // Delete old avatar if exists
        if ($user->avatar && file_exists(public_path('storage/' . $user->avatar))) {
            unlink(public_path('storage/' . $user->avatar));
        }
        
        // Store new avatar
        $avatarPath = $avatarFile->store('avatars', 'public');
        
        // Update user avatar
        $user->update(['avatar' => $avatarPath]);
        
        return $avatarPath;
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletionPercentage(int $userId): int
    {
        $user = User::with('sellerProfile')->findOrFail($userId);
        $totalFields = $user->isSeller() ? 8 : 4; // User fields + seller profile fields
        $completedFields = 0;
        
        // Check user fields
        if (!empty($user->name)) $completedFields++;
        if (!empty($user->email)) $completedFields++;
        if (!empty($user->phone)) $completedFields++;
        if (!empty($user->avatar)) $completedFields++;
        
        // Check seller profile fields if seller
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
            
            // Verify current password
            if (!Hash::check($currentPassword, $user->password)) {
                throw new \Exception('Password saat ini tidak sesuai.');
            }
            
            // Update password
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
    
}