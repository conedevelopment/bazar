<?php

namespace Cone\Bazar;

use Cone\Root\Root;
use Illuminate\Support\ServiceProvider;

abstract class BazarApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerResources();
    }

    /**
     * Register the resources.
     */
    protected function registerResources(): void
    {
        $this->app->make(Root::class)->resources->register($this->resources());
    }

    /**
     * The resources.
     */
    protected function resources(): array
    {
        return [
            //
        ];
    }
}
