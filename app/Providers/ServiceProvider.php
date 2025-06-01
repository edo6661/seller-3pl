<?php
namespace App\Providers;

use App\Services\AuthService;
use App\Services\BuyerRatingService;
use App\Services\NotificationService;
use App\Services\PickupService;
use App\Services\ProductService;
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
        ProductService::class => ProductService::class,
        WalletService::class => WalletService::class,
        NotificationService::class => NotificationService::class,
        BuyerRatingService::class => BuyerRatingService::class,
        AuthService::class => AuthService::class,
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
            ProductService::class,
            PickupService::class,
            WalletService::class,
            NotificationService::class,
            BuyerRatingService::class,
            WithdrawService::class,
            AuthService::class,
        ];
    }
}