<?php

namespace App\Services;

use App\Models\SellerProfile;

class SellerProfileService
{
    public function getUserProfile(int $userId): ?SellerProfile
    {
        return SellerProfile::where('user_id', $userId)->first();
    }

    public function createProfile(array $data): SellerProfile
    {
        return SellerProfile::create($data);
    }

    public function updateProfile(int $userId, array $data): SellerProfile
    {
        $profile = SellerProfile::where('user_id', $userId)->firstOrFail();
        $profile->update($data);
        return $profile;
    }

    public function getOrCreateProfile(int $userId): SellerProfile
    {
        $profile = $this->getUserProfile($userId);
        
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

    public function hasProfile(int $userId): bool
    {
        return SellerProfile::where('user_id', $userId)->exists();
    }

    public function isProfileComplete(int $userId): bool
    {
        $profile = $this->getUserProfile($userId);
        return $profile ? $profile->is_profile_complete : false;
    }

    public function updateCoordinates(int $userId, float $latitude, float $longitude): SellerProfile
    {
        $profile = SellerProfile::where('user_id', $userId)->firstOrFail();
        $profile->update([
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
        return $profile;
    }
}