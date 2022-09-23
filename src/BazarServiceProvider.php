<?php

namespace Cone\Bazar;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class BazarServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        Interfaces\Models\Address::class => Models\Address::class,
        Interfaces\Models\Cart::class => Models\Cart::class,
        Interfaces\Models\Category::class => Models\Category::class,
        Interfaces\Models\Item::class => Models\Item::class,
        Interfaces\Models\Order::class => Models\Order::class,
        Interfaces\Models\Product::class => Models\Product::class,
        Interfaces\Models\Shipping::class => Models\Shipping::class,
        Interfaces\Models\Transaction::class => Models\Transaction::class,
        Interfaces\Models\Variant::class => Models\Variant::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        Interfaces\Cart\Manager::class => Cart\Manager::class,
        Interfaces\Gateway\Manager::class => Gateway\Manager::class,
        Interfaces\Repositories\DiscountRepository::class => Repositories\DiscountRepository::class,
        Interfaces\Repositories\TaxRepository::class => Repositories\TaxRepository::class,
        Interfaces\Shipping\Manager::class => Shipping\Manager::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/bazar.php', 'bazar');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerEvents();
        $this->registerMacros();
        $this->registerLoadings();
        $this->registerCommands();
        $this->registerPublishes();
    }

    /**
     * Register routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        if (! $this->app->routesAreCached()) {
            $this->app['router']
                 ->get('bazar/download', Http\Controllers\DownloadController::class)
                 ->name('bazar.download')
                 ->middleware('signed');
        }
    }

    /**
     * Register loadings.
     *
     * @return void
     */
    protected function registerLoadings(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bazar');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register publishes.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/bazar.php' => $this->app->configPath('bazar.php'),
            ], 'bazar-config');

            $this->publishes([
                __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/bazar'),
            ], 'bazar-views');
        }
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\Install::class,
                Console\Commands\ClearCarts::class,
            ]);
        }
    }

    /**
     * Register macros.
     *
     * @return void
     */
    protected function registerMacros(): void
    {
        Str::macro('currency', static function ($value, string $currency = null): string {
            return sprintf(
                '%s %s', number_format($value, 2), strtoupper($currency ?: Bazar::getCurrency())
            );
        });
    }

    /**
     * Register events.
     *
     * @return void
     */
    protected function registerEvents(): void
    {
        $this->app['events']->listen(Logout::class, Listeners\ClearCookies::class);
        $this->app['events']->listen(Events\CheckoutFailed::class, Listeners\HandleFailedCheckout::class);
        $this->app['events']->listen(Events\CheckoutProcessed::class, Listeners\PlaceOrder::class);
        $this->app['events']->listen(Events\CheckoutProcessed::class, Listeners\RefreshInventory::class);
        $this->app['events']->listen(Events\CheckoutProcessing::class, Listeners\HandleProcessingCheckout::class);
    }
}
