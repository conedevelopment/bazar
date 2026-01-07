<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Traits;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;

class AsCustomerTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_user_has_carts(): void
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($this->user->carts->contains($cart));
    }

    public function test_user_has_active_cart(): void
    {
        $cart1 = Cart::factory()->create(['user_id' => $this->user->id]);
        $cart2 = Cart::factory()->create(['user_id' => $this->user->id]);

        $activeCart = $this->user->cart;

        $this->assertInstanceOf(Cart::class, $activeCart);
        $this->assertSame($cart2->id, $activeCart->id);
    }

    public function test_user_has_orders(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($this->user->orders->contains($order));
    }

    public function test_user_has_addresses(): void
    {
        $address = Address::factory()->create([
            'addressable_id' => $this->user->id,
            'addressable_type' => get_class($this->user),
        ]);

        $this->assertTrue($this->user->addresses->contains($address));
    }

    public function test_user_has_default_address(): void
    {
        $address1 = Address::factory()->create([
            'addressable_id' => $this->user->id,
            'addressable_type' => get_class($this->user),
            'default' => false,
        ]);

        $address2 = Address::factory()->create([
            'addressable_id' => $this->user->id,
            'addressable_type' => get_class($this->user),
            'default' => true,
        ]);

        $defaultAddress = $this->user->address;

        $this->assertInstanceOf(Address::class, $defaultAddress);
        $this->assertSame($address2->id, $defaultAddress->id);
    }
}
