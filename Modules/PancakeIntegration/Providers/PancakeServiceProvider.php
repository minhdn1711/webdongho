<?php

namespace Modules\PancakeIntegration\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\CategorySaved;
use Modules\PancakeIntegration\Listeners\PancakeCategoryListener;

class PancakeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        // Register Listeners
        Event::listen(
            \App\Events\ProductSaved::class,
            \Modules\PancakeIntegration\Listeners\PancakeProductListener::class
        );

        Event::listen(
            \App\Events\OrderPlaced::class,
            \Modules\PancakeIntegration\Listeners\PancakeOrderListener::class
        );

        Event::listen(
            CategorySaved::class,
            PancakeCategoryListener::class
        );
    }
}
