<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Taxable;
use Bazar\Models\Cart;
use Bazar\Models\Item;
use Bazar\Models\Product;
use Bazar\Support\Facades\Tax;
use Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class ItemTest extends TestCase
{
    protected $item;

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

        $this->item->buyable()->associate($product)->itemable()->associate($cart)->save();
    }

    /** @test */
    public function an_item_is_taxable()
    {
        $this->assertInstanceOf(Taxable::class, $this->item);
        $this->assertSame(Str::currency($this->item->tax, $this->item->itemable->currency), $this->item->getFormattedTax());
        $this->assertSame($this->item->getFormattedTax(), $this->item->formattedTax);
    }

    /** @test */
    public function an_item_has_price_attribute()
    {
        $this->assertSame($this->item->price, $this->item->getPrice());
        $this->assertSame(
            Str::currency($this->item->price, $this->item->itemable->currency), $this->item->getFormattedPrice()
        );
        $this->assertSame($this->item->getFormattedPrice(), $this->item->formattedPrice);
    }

    /** @test */
    public function an_item_has_total_attribute()
    {
        $this->assertSame(
            ($this->item->price + $this->item->tax) * $this->item->quantity,
            $this->item->getTotal()
        );
        $this->assertSame($this->item->getTotal(), $this->item->total);
        $this->assertSame(
            Str::currency($this->item->total, $this->item->itemable->currency),
            $this->item->getFormattedTotal()
        );
        $this->assertSame($this->item->getFormattedTotal(), $this->item->formattedTotal);
        $this->assertSame($this->item->price * $this->item->quantity, $this->item->getNetTotal());
        $this->assertSame($this->item->getNetTotal(), $this->item->netTotal);
        $this->assertSame(
            Str::currency($this->item->netTotal, $this->item->itemable->currency),
            $this->item->getFormattedNetTotal()
        );
        $this->assertSame($this->item->getFormattedNetTotal(), $this->item->formattedNetTotal);
    }
}
