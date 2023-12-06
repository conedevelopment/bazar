<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Interfaces\Tax as Contract;
use Cone\Bazar\Interfaces\Taxable;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Facades\Tax;
use Cone\Bazar\Tests\TestCase;

class TaxTest extends TestCase
{
    protected Cart $cart;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();

        Product::factory()->count(2)->create()->each(function ($product) {
            $this->cart->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'price' => $product->price,
                'tax' => 0,
                'quantity' => 1,
                'name' => $product->name,
            ]);
        });

        Tax::register('custom-30', 30);
    }

    public function test_tax_repository_calculates_taxes(): void
    {
        Tax::register('custom-object', new CustomTax());
        Tax::register('custom-closure', function (Taxable $model) {
            return $model instanceof Shipping ? 20 : 30;
        });

        $this->assertEquals(470, $this->cart->calculateTax());
    }

    public function test_tax_repository_removes_taxes(): void
    {
        Tax::remove('custom-30');

        $this->assertEquals(0, $this->cart->calculateTax());
    }

    public function test_tax_repository_disables_taxes(): void
    {
        $this->assertEquals(90, $this->cart->calculateTax());

        Tax::disable();

        Tax::register('custom-10', 10);

        $this->assertEquals(90, $this->cart->calculateTax());

        Tax::enable();

        $this->assertEquals(120, $this->cart->calculateTax());
    }
}

class CustomTax implements Contract
{
    public function __invoke(Taxable $model): float
    {
        return 100;
    }
}
