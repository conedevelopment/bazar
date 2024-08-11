<?php

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Interfaces\Taxable;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Support\Facades\Tax;
use Cone\Bazar\Tests\TestCase;

class ItemTest extends TestCase
{
    protected Item $item;

    public function setUp(): void
    {
        parent::setUp();

        Tax::register('fix-10%', function (Taxable $item) {
            return $item->price * 0.1;
        });

        $cart = Cart::factory()->create();
        $product = Product::factory()->create();

        $this->item = Item::factory()->make([
            'properties' => ['text' => 'test-text'],
        ]);

        $this->item->buyable()->associate($product)->checkoutable()->associate($cart)->save();
    }

    public function test_item_is_taxable(): void
    {
        $this->assertInstanceOf(Taxable::class, $this->item);
        $this->assertSame(
            (new Currency($this->item->tax, $this->item->checkoutable->currency))->format(),
            $this->item->getFormattedTax()
        );
        $this->assertSame($this->item->getFormattedTax(), $this->item->formattedTax);
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
