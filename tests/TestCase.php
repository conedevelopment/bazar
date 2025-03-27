<?php

namespace Cone\Bazar\Tests;

use Cone\Root\Interfaces\Models\User as UserContract;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    public function createApplication(): Application
    {
        $app = require __DIR__.'/app.php';

        $app->booting(static function () use ($app): void {
            $app->bind(UserContract::class, User::class);
        });

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->startSession();

        $this->app['request']->setLaravelSession(
            $this->app['session']->driver()
        );
    }
}
