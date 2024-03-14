<?php

namespace Cone\Bazar;

use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\ServiceProvider;

class BazarServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     */
    public array $bindings = [
        Interfaces\Models\Address::class => Models\Address::class,
        Interfaces\Models\Cart::class => Models\Cart::class,
        Interfaces\Models\Category::class => Models\Category::class,
        Interfaces\Models\Item::class => Models\Item::class,
        Interfaces\Models\Order::class => Models\Order::class,
        Interfaces\Models\Product::class => Models\Product::class,
        Interfaces\Models\Property::class => Models\Property::class,
        Interfaces\Models\PropertyValue::class => Models\PropertyValue::class,
        Interfaces\Models\Shipping::class => Models\Shipping::class,
        Interfaces\Models\Transaction::class => Models\Transaction::class,
        Interfaces\Models\Variant::class => Models\Variant::class,
    ];

    /**
     * All of the container singletons that should be registered.
     */
    public array $singletons = [
        Interfaces\Cart\Manager::class => Cart\Manager::class,
        Interfaces\Gateway\Manager::class => Gateway\Manager::class,
        Interfaces\Repositories\DiscountRepository::class => Repositories\DiscountRepository::class,
        Interfaces\Repositories\TaxRepository::class => Repositories\TaxRepository::class,
        Interfaces\Shipping\Manager::class => Shipping\Manager::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/bazar.php', 'bazar');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            $this->registerCommands();
            $this->registerPublishes();
        }

        if (! $this->app->routesAreCached()) {
            $this->registerRoutes();
        }

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'bazar');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bazar');

        $this->registerEvents();
    }

    /**
     * Register routes.
     */
    protected function registerRoutes(): void
    {
        $this->app['router']
            ->prefix('bazar')
            ->as('bazar.')
            ->group(function (): void {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            });
    }

    /**
     * Register publishes.
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../config/bazar.php' => $this->app->configPath('bazar.php'),
        ], 'bazar-config');

        $this->publishes([
            __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/bazar'),
        ], 'bazar-views');

        $this->publishes([
            __DIR__.'/../stubs/BazarServiceProvider.stub' => $this->app->basePath('app/Providers/BazarServiceProvider.php'),
            __DIR__.'/../stubs/CategoryResource.stub' => $this->app->basePath('app/Root/Resources/CategoryResource.php'),
            __DIR__.'/../stubs/ProductResource.stub' => $this->app->basePath('app/Root/Resources/ProductResource.php'),
            __DIR__.'/../stubs/PropertyResource.stub' => $this->app->basePath('app/Root/Resources/PropertyResource.php'),
            __DIR__.'/../stubs/OrderResource.stub' => $this->app->basePath('app/Root/Resources/OrderResource.php'),
        ], 'bazar-stubs');
    }

    /**
     * Register commands.
     */
    protected function registerCommands(): void
    {
        AboutCommand::add('Bazar', fn (): array => ['Version' => Bazar::VERSION]);

        $this->commands([
            Console\Commands\Install::class,
            Console\Commands\ClearCarts::class,
        ]);
    }

    /**
     * Register events.
     */
    protected function registerEvents(): void
    {
        $this->app['events']->listen(Logout::class, Listeners\ClearCookies::class);
        $this->app['events']->listen(Events\PaymentCaptured::class, Listeners\RefreshInventory::class);
        $this->app['events']->listen(VendorTagPublished::class, Listeners\FormatBazarStubs::class);
    }
}
