<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials, bool $remember = false): bool
    {
        $user = $this->getUserByEmail($credentials['email']);
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        Auth::login($user, $remember);
        
        return true;
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['email'] = strtolower($data['email']);
        
        return User::create($data);
    }

    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }
        
        $user->update($data);
        return $user;
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = User::findOrFail($userId);
        
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak valid.'],
            ]);
        }
        
        $user->update(['password' => Hash::make($newPassword)]);
        return true;
    }

    public function checkUserRole(string $role): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->role->value === $role;
    }

    public function redirectAfterLogin(): string
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return route('guest.auth.login');
        }
        
        if ($user->isAdmin()) {
            return route('admin.buyer-ratings.index');
        }
        
        if ($user->isSeller()) {
            return route('guest.home');
        }
        
        return route('guest.home');
    }

    public function getUserStats(int $userId): array
    {
        $user = User::findOrFail($userId);
        
        $stats = [
            'total_products' => $user->products()->count(),
            'active_products' => $user->products()->where('is_active', true)->count(),
            'total_pickup_requests' => $user->pickupRequests()->count(),
            'pending_pickup_requests' => $user->pickupRequests()->where('status', 'pending')->count(),
        ];
        
        if ($user->wallet) {
            $stats['wallet_balance'] = $user->wallet->balance;
        }
        
        return $stats;
    }

    public function deleteUser(int $id): bool
    {
        $user = User::find($id);
        
        if ($user) {
            
            if ($user->products()->exists() || $user->pickupRequests()->exists()) {
                
                $user->update(['is_active' => false]);
                return true;
            }
            
            return $user->delete();
        }
        
        return false;
    }

    public function searchUsers(string $search): \Illuminate\Database\Eloquent\Collection
    {
        return User::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->get();
    }
        public function handleProviderCallback(string $provider, object $socialUser): User
        {
            return DB::transaction(function () use ($provider, $socialUser) {
                
                
                $socialAccount = SocialAccount::where('provider', $provider)
                    ->where('provider_id', $socialUser->getId())
                    ->first();
                
                
                if ($socialAccount && $socialAccount->user) {
                    
                    $socialAccount->update([
                        'provider_token' => $socialUser->token,
                        'provider_refresh_token' => $socialUser->refreshToken,
                    ]);
                    
                    return $socialAccount->user;
                }
                
                
                $user = $this->getUserByEmail($socialUser->getEmail());
                
                
                if (!$user) {
                    $user = User::create([
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        'avatar' => $socialUser->getAvatar(),
                        'password' => null,
                        'email_verified_at' => now(),
                        'role' => 'seller',
                    ]);
                }
                
                
                $socialAccount = SocialAccount::updateOrCreate(
                    [
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                    ],
                    [
                        'user_id' => $user->id, 
                        'provider_token' => $socialUser->token,
                        'provider_refresh_token' => $socialUser->refreshToken,
                    ]
                );
                
                return $user;
            });
        }
    public function createUserWithVerification(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['email'] = strtolower($data['email']);
        
        $user = User::create($data);
        
        event(new UserRegistered($user));
        
        return $user;
    }
}