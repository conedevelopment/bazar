<?php

namespace Bazar\Tests;

use Bazar\BazarServiceProvider;
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

        $app->booting(function () use ($app) {
            $app->register(BazarServiceProvider::class);

            if (isset($_SERVER['BAZAR_PROVIDER']) && class_exists($_SERVER['BAZAR_PROVIDER'])) {
                $app->register($_SERVER['BAZAR_PROVIDER']);
            }
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
