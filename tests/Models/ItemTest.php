<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\TaxRate;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Tests\TestCase;

class ItemTest extends TestCase
{
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $cart = Cart::factory()->create();
        $product = Product::factory()->create();

        $taxRate = TaxRate::factory()->create();
        $product->taxRates()->attach($taxRate);

        $this->item = Item::factory()->make([
            'properties' => ['text' => 'test-text'],
        ]);

        $this->item->buyable()->associate($product)->checkoutable()->associate($cart)->save();
    }

    public function test_item_is_taxable(): void
    {
        $this->assertSame(
            $this->item->calculateTaxes(),
            $this->item->getTaxTotal()
        );
    }

    public function test_item_has_price_attribute(): void
    {
        $this->assertSame($this->item->price, $this->item->getPrice());
        $this->assertSame(
            (new Currency($this->item->price, $this->item->checkoutable->currency))->format(),
            $this->item->getFormattedPrice()
        );
        $this->assertSame($this->item->getFormattedPrice(), $this->item->formattedPrice);
    }

    public function test_item_has_total_attribute(): void
    {
        $this->assertSame(
            ($this->item->price + $this->item->tax) * $this->item->quantity,
            $this->item->getTotal()
        );
        $this->assertSame($this->item->getTotal(), $this->item->total);
        $this->assertSame(
            (new Currency($this->item->total, $this->item->checkoutable->currency))->format(),
            $this->item->getFormattedTotal()
        );
        $this->assertSame($this->item->getFormattedTotal(), $this->item->formattedTotal);
        $this->assertSame($this->item->price * $this->item->quantity, $this->item->getSubtotal());
        $this->assertSame($this->item->getSubtotal(), $this->item->subtotal);
        $this->assertSame(
            (new Currency($this->item->subtotal, $this->item->checkoutable->currency))->format(),
            $this->item->getFormattedSubtotal()
        );
        $this->assertSame($this->item->getFormattedSubtotal(), $this->item->formattedSubtotal);
    }
}
