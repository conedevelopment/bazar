<?php

namespace Bazar\Tests;

use Bazar\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $admin, $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->registerMacros();

        $this->app['config']->set('auth.providers.users.model', User::class);
        $this->app['config']->set('bazar.admins', ['admin@bazar.test']);

        Queue::fake();
        Storage::fake('local');
        Storage::fake('public');

        $this->withoutMix();

        $this->admin = factory(User::class)->create(['email' => 'admin@bazar.test']);
        $this->user = factory(User::class)->create();
    }

    protected function registerMacros(): void
    {
        TestResponse::macro('props', function ($key = null) {
            $props = json_decode(json_encode($this->original->getData()['page']['props']), JSON_OBJECT_AS_ARRAY);

            if ($key) {
                return Arr::get($props, $key);
            }

            return $props;
        });

        TestResponse::macro('assertComponent', function ($component) {
            Assert::assertEquals($this->original->getData()['page']['component'], $component);

            return $this;
        });

        TestResponse::macro('assertHasProp', function ($key) {
            Assert::assertTrue(Arr::has($this->props(), $key));

            return $this;
        });

        TestResponse::macro('assertPropValue', function ($key, $value) {
            $this->assertHasProp($key);

            if (is_callable($value)) {
                $value($this->props($key));
            } else {
                Assert::assertEquals($this->props($key), $value);
            }

            return $this;
        });
    }
}
