<?php

namespace Cone\Bazar\Tests;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Category;
use Cone\Bazar\Models\Medium;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Models\User;
use Cone\Bazar\Models\Variant;
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
        $this->app['config']->set('bazar.admins', ['test@bazar.test']);

        Storage::fake('local');
        Storage::fake('public');

        $this->admin = User::factory()->create(['email' => 'test@bazar.test']);
        $this->user = User::factory()->create();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(User::class, ModelPolicy::class);
        Gate::policy(Product::class, ModelPolicy::class);
        Gate::policy(Variant::class, ModelPolicy::class);
        Gate::policy(Address::class, ModelPolicy::class);
        Gate::policy(Order::class, ModelPolicy::class);
        Gate::policy(Medium::class, ModelPolicy::class);
        Gate::policy(Transaction::class, ModelPolicy::class);
        Gate::policy(Category::class, ModelPolicy::class);
    }
}
