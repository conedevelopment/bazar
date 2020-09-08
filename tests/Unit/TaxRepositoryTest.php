<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Tax as Contract;
use Bazar\Contracts\Taxable;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Models\Shipping;
use Bazar\Support\Facades\Tax;
use Bazar\Tests\TestCase;

class TaxRepositoryTest extends TestCase
{
    protected $cart;

    public function setUp(): void
    {
        parent::setUp();

        $products = ProductFactory::new()->count(2)->create()->mapWithKeys(function ($product) {
            return [$product->id => ['price' => $product->price, 'tax' => 0, 'quantity' => 1]];
        })->toArray();

        $this->cart = CartFactory::new()->create();
        $this->cart->products()->attach($products);

        Tax::register('custom-30', 30);
    }

    /** @test */
    public function it_can_calculate_taxes()
    {
        Tax::register('custom-object', new CustomTax);
        Tax::register('custom-class', CustomTax::class);
        Tax::register('custom-closure', function (Taxable $model) {
            return $model instanceof Shipping ? 20 : 30;
        });

        $this->assertEquals(770, $this->cart->tax());
    }

    /** @test */
    public function it_can_remove_taxes()
    {
        Tax::remove('custom-30');

        $this->assertEquals(0, $this->cart->tax());
    }

    /** @test */
    public function it_can_disable_taxes()
    {
        $this->assertEquals(90, $this->cart->tax());

        Tax::disable();

        Tax::register('custom-10', 10);

        $this->assertEquals(90, $this->cart->tax());

        Tax::enable();

        $this->assertEquals(120, $this->cart->tax());
    }
}

class CustomTax implements Contract
{
    public function calculate(Taxable $model): float
    {
        return 100;
    }
}
