<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Tax;
use Cone\Bazar\Models\TaxRate;
use Cone\Bazar\Tests\TestCase;

class TaxTest extends TestCase
{
    protected Cart $cart;

    protected Item $item;

    protected TaxRate $taxRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();
        $product = Product::factory()->create();

        $this->item = $this->cart->items()->create([
            'buyable_id' => $product->id,
            'buyable_type' => Product::class,
            'quantity' => 2,
            'price' => 100,
            'name' => $product->name,
        ]);

        $this->taxRate = TaxRate::factory()->create(['rate' => 27]);
        $product->taxRates()->attach($this->taxRate);
        $this->cart->refresh();
    }

    public function test_tax_has_value(): void
    {
        $this->cart->calculateTax();

        $tax = Tax::first();

        $this->assertNotNull($tax);
        $this->assertIsFloat($tax->value);
        $this->assertGreaterThan(0, $tax->value);
    }

    public function test_tax_formats_value(): void
    {
        $this->cart->calculateTax();

        $tax = Tax::first();

        $formatted = $tax->format();

        $this->assertIsString($formatted);
    }

    public function test_tax_has_default_value(): void
    {
        $tax = new Tax();

        $this->assertEquals(0, $tax->value);
    }
}
