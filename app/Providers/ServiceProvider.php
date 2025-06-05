<?php
namespace App\Providers;

use App\Services\AuthService;
use App\Services\BuyerRatingService;
use App\Services\EmailVerificationService;
use App\Services\MidtransService;
use App\Services\NotificationService;
use App\Services\PasswordResetService;
use App\Services\PickupRequestService;
use App\Services\PickupService;
use App\Services\ProductService;
use App\Services\ProfileService;
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
        MidtransService::class => MidtransService::class,
        NotificationService::class => NotificationService::class,
        PasswordResetService::class => PasswordResetService::class,
        PickupService::class => PickupService::class,
        PickupRequestService::class => PickupRequestService::class,
        ProductService::class => ProductService::class,
        ProfileService::class => ProfileService::class,
        UserService::class => UserService::class,
        WalletService::class => WalletService::class,
        WithdrawService::class => WithdrawService::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        
        $this->app->singleton(PickupService::class, function ($app) {
            return new PickupService(
                $app->make(BuyerRatingService::class),
                $app->make(NotificationService::class),
                $app->make(WalletService::class)
            );
        });

        $this->app->singleton(WithdrawService::class, function ($app) {
            return new WithdrawService(
                $app->make(WalletService::class),
                $app->make(NotificationService::class)
            );
        });
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                $app->make(EmailVerificationService::class),
            );
        });


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
            MidtransService::class,
            NotificationService::class,
            PasswordResetService::class,
            PickupService::class,
            PickupRequestService::class,
            ProductService::class,
            ProfileService::class,
            UserService::class,
            WalletService::class,
            WithdrawService::class,
            
        ];
    }
}