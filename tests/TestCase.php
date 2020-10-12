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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $admin, $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMix();
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
}
