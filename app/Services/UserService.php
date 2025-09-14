<?php
namespace App\Services;

use App\Enums\SellerVerificationStatus;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Pagination\LengthAwarePaginator;
class UserService
{
    public function getAllUsers(?string $search, ?string $role, ?string $status): LengthAwarePaginator
    {
        $query = User::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }
        if ($status === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($status === 'unverified') {
            $query->whereNull('email_verified_at');
        }
        return $query->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();
    }
    public function getUserStats(): array
    {
        $total = User::count();
        $sellers = User::where('role', UserRole::SELLER)->count();
        $admins = User::where('role', UserRole::ADMIN)->count();
        $verified = User::whereNotNull('email_verified_at')->count();
        $unverified = User::whereNull('email_verified_at')->count();
        return [
            'total' => $total,
            'sellers' => $sellers,
            'admins' => $admins,
            'verified' => $verified,
            'unverified' => $unverified
        ];
    }
    public function getUserById(int $id): ?User
    {
        return User::with(['sellerProfile', 'wallet', 'products'])->find($id);
    }
    public function approveVerification(User $user): bool
    {
        if ($user->isSeller() && $user->sellerProfile) {
            $user->sellerProfile->update(attributes: [
                'verification_status' => SellerVerificationStatus::VERIFIED,
                'verification_notes' => null,
            ]);
            return true;
        }
        return false;
    }
    public function rejectVerification(User $user, string $notes): bool
        {
            if ($user->isSeller() && $user->sellerProfile) {
                $user->sellerProfile->update([
                    'verification_status' => SellerVerificationStatus::REJECTED,
                    'verification_notes' => $notes,
                ]);
                return true;
            }
            return false;
        }
}