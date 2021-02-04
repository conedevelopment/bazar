<?php

namespace Bazar;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route as RouteFactory;
use Illuminate\Support\Facades\View as ViewFactory;
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
        Contracts\Models\User::class => Models\User::class,
        Contracts\Models\Cart::class => Models\Cart::class,
        Contracts\Models\Order::class => Models\Order::class,
        Contracts\Models\Medium::class => Models\Medium::class,
        Contracts\Models\Product::class => Models\Product::class,
        Contracts\Models\Variant::class => Models\Variant::class,
        Contracts\Models\Address::class => Models\Address::class,
        Contracts\Models\Category::class => Models\Category::class,
        Contracts\Models\Shipping::class => Models\Shipping::class,
        Contracts\Models\Transaction::class => Models\Transaction::class,
        Contracts\Http\ResponseFactory::class => Http\ResponseFactory::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        Contracts\Cart\Manager::class => Cart\Manager::class,
        Contracts\Gateway\Manager::class => Gateway\Manager::class,
        Contracts\Shipping\Manager::class => Shipping\Manager::class,
        Contracts\Conversion\Manager::class => Conversion\Manager::class,
        Contracts\Repositories\TaxRepository::class => Repositories\TaxRepository::class,
        Contracts\Repositories\AssetRepository::class => Repositories\AssetRepository::class,
        Contracts\Repositories\DiscountRepository::class => Repositories\DiscountRepository::class,
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
        $this->registerAuth();
        $this->registerRoutes();
        $this->registerEvents();
        $this->registerMacros();
        $this->registerLoadings();
        $this->registerCommands();
        $this->registerPublishes();
        $this->registerComposers();
        $this->registerConversions();
        $this->registerRouteBindings();
    }

    /**
     * Register routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        if (! $this->app->routesAreCached()) {
            Bazar::routes(function (): void {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            });

            RouteFactory::get('bazar/download', Http\Controllers\DownloadController::class)
                ->middleware('signed')
                ->name('bazar.download');
        }
    }

    /**
     * Register the route bindings.
     *
     * @return void
     */
    public function registerRouteBindings(): void
    {
        foreach ($this->bindings as $contract => $abstract) {
            if (is_subclass_of($abstract, Model::class)) {
                $key = strtolower(class_basename($contract));

                RouteFactory::bind($key, function (string $value, Route $route) use ($key, $contract): ?Model {
                    return $this->app->make($contract)->resolveRouteBinding(
                        $value, $route->bindingFieldFor($key)
                    );
                });
            }
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
                __DIR__.'/../config/bazar.php' => App::configPath('bazar.php'),
            ], 'bazar-config');

            $this->publishes([
                __DIR__.'/../resources/js' => App::resourcePath('js/vendor/bazar'),
                __DIR__.'/../resources/sass' => App::resourcePath('sass/vendor/bazar'),
            ], 'bazar-assets');

            $this->publishes([
                __DIR__.'/../resources/views' => App::resourcePath('views/vendor/bazar'),
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
                Console\Commands\InstallCommand::class,
                Console\Commands\PublishCommand::class,
                Console\Commands\LinkAssetsCommand::class,
                Console\Commands\ClearCartsCommand::class,
                Console\Commands\ClearChunksCommand::class,
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
                '%s %s', number_format($value, 2), strtoupper($currency ?: Bazar::currency())
            );
        });
    }

    /**
     * Register the view composers.
     *
     * @return void
     */
    protected function registerComposers(): void
    {
        ViewFactory::composer('bazar::*', function (View $view): void {
            $view->with('translations', (object) $this->app['translator']->getLoader()->load(
                $this->app->getLocale(), '*', '*'
            ));
        });
    }

    /**
     * Register the image conversions.
     *
     * @return void
     */
    protected function registerConversions(): void
    {
        Support\Facades\Conversion::register('thumb', static function (Conversion\Image $image): void {
            $image->crop(500, 500);
        });

        Support\Facades\Conversion::register('medium', static function (Conversion\Image $image): void {
            $image->resize(1400, 1000);
        });
    }

    /**
     * Register the default authorization.
     *
     * @return void
     */
    protected function registerAuth(): void
    {
        Gate::define('manage-bazar', static function (Contracts\Models\User $user): bool {
            return $user->isAdmin();
        });
    }

    /**
     * Register events.
     *
     * @return void
     */
    protected function registerEvents(): void
    {
        Event::listen(Logout::class, Listeners\ClearCookies::class);
        Event::listen(Events\CartTouched::class, Listeners\RefreshCart::class);
        Event::listen(Events\CheckoutProcessed::class, Listeners\PlaceOrder::class);
        Event::listen(Events\OrderPlaced::class, Listeners\RefreshInventory::class);
        Event::listen(Events\OrderPlaced::class, Listeners\SendNewOrderNotifications::class);
        Event::listen(Events\CheckoutFailing::class, Listeners\HandleFailingCheckout::class);
        Event::listen(Events\CheckoutProcessing::class, Listeners\HandleProcessingCheckout::class);
    }
}
