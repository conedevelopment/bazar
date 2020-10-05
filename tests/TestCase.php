<?php

namespace Bazar\Tests;

use Bazar\Database\Factories\UserFactory;
use Bazar\Models\Address;
use Bazar\Models\Category;
use Bazar\Models\Medium;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\Transaction;
use Bazar\Models\User;
use Bazar\Models\Variation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
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

        $this->withoutMix();
        $this->registerMacros();
        $this->registerPolicies();

        $this->app['config']->set('auth.providers.users.model', User::class);
        $this->app['config']->set('bazar.admins', ['admin@bazar.test']);

        Storage::fake('local');
        Storage::fake('public');

        $this->admin = UserFactory::new()->create(['email' => 'admin@bazar.test']);
        $this->user = UserFactory::new()->create();
    }

    public function registerPolicies(): void
    {
        Gate::policy(User::class, ModelPolicy::class);
        Gate::policy(Product::class, ModelPolicy::class);
        Gate::policy(Variation::class, ModelPolicy::class);
        Gate::policy(Address::class, ModelPolicy::class);
        Gate::policy(Order::class, ModelPolicy::class);
        Gate::policy(Medium::class, ModelPolicy::class);
        Gate::policy(Transaction::class, ModelPolicy::class);
        Gate::policy(Category::class, ModelPolicy::class);
    }

    protected function registerMacros(): void
    {
        TestResponse::macro('props', function ($key = null) {
            $props = json_decode(json_encode($this->original->getData()['page']['props']), JSON_OBJECT_AS_ARRAY);

            return $key ? Arr::get($props, $key) : $props;
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
