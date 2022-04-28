<?php

namespace Cone\Bazar\Tests;

use Cone\Bazar\BazarServiceProvider;
use Cone\Root\Interfaces\Models\User as UserContract;
use Cone\Root\RootServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->booting(static function () use ($app): void {
            $app->register(RootServiceProvider::class);
            $app->register(BazarServiceProvider::class);

            $app->bind(UserContract::class, User::class);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
