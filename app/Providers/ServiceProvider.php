<?php
namespace App\Providers;

use App\Services\AdminDashboardService;
use App\Services\ApiAuthService;
use App\Services\AuthService;
use App\Services\BuyerRatingService;
use App\Services\EmailVerificationService;
use App\Services\MidtransService;
use App\Services\NotificationService;
use App\Services\PasswordResetService;
use App\Services\PickupRequestService;
use App\Services\PickupService;
use App\Services\ProductExportService;
use App\Services\ProductService;
use App\Services\ProfileService;
use App\Services\TeamService;
use App\Services\UserService;
use App\Services\WalletService;
use App\Services\WithdrawService;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array<string, string>
     */
    public array $singletons = [
        AuthService::class => AuthService::class,
        BuyerRatingService::class => BuyerRatingService::class,
        EmailVerificationService::class => EmailVerificationService::class,
        NotificationService::class => NotificationService::class,
        PasswordResetService::class => PasswordResetService::class,
        PickupService::class => PickupService::class,
        PickupRequestService::class => PickupRequestService::class,
        ProductService::class => ProductService::class,
        ProfileService::class => ProfileService::class,
        UserService::class => UserService::class,
        WalletService::class => WalletService::class,
        ApiAuthService::class => ApiAuthService::class,
        AdminDashboardService::class => AdminDashboardService::class,
        ProductExportService::class => ProductExportService::class,
        TeamService::class => TeamService::class,
        NotificationService::class => NotificationService::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            AuthService::class,
            BuyerRatingService::class,
            EmailVerificationService::class,
            NotificationService::class,
            PasswordResetService::class,
            PickupService::class,
            PickupRequestService::class,
            ProductService::class,
            ProfileService::class,
            UserService::class,
            WalletService::class,
            ApiAuthService::class,
            AdminDashboardService::class,
            ProductExportService::class,
            TeamService::class,
            NotificationService::class,

        ];
    }
}