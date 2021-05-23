<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Stockable;
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

        Item::resolvePropertyUsing('text', function (Item $item, string $value) {
            return $item->price += mb_strlen($value) * 0.1;
        });

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
        $this->assertSame(Str::currency($this->item->tax, $this->item->itemable->currency), $this->item->formattedTax());
        $this->assertSame($this->item->formattedTax(), $this->item->formattedTax);
    }

    /** @test */
    public function an_item_has_stockable_attribute()
    {
        $this->assertInstanceOf(Stockable::class, $this->item->stockable);
    }

    /** @test */
    public function an_item_has_price_attribute()
    {
        $this->assertSame($this->item->buyable->price('sale') + 0.9, $this->item->price);
        $this->assertSame($this->item->price, $this->item->price());
        $this->assertSame(
            Str::currency($this->item->price, $this->item->itemable->currency), $this->item->formattedPrice()
        );
        $this->assertSame($this->item->formattedPrice(), $this->item->formattedPrice);
    }

    /** @test */
    public function an_item_has_total_attribute()
    {
        $this->assertSame(
            ($this->item->price + $this->item->tax) * $this->item->quantity,
            $this->item->total()
        );
        $this->assertSame($this->item->total(), $this->item->total);
        $this->assertSame(
            Str::currency($this->item->total, $this->item->itemable->currency),
            $this->item->formattedTotal()
        );
        $this->assertSame($this->item->formattedTotal(), $this->item->formattedTotal);
        $this->assertSame($this->item->price * $this->item->quantity, $this->item->netTotal());
        $this->assertSame($this->item->netTotal(), $this->item->netTotal);
        $this->assertSame(
            Str::currency($this->item->netTotal, $this->item->itemable->currency),
            $this->item->formattedNetTotal()
        );
        $this->assertSame($this->item->formattedNetTotal(), $this->item->formattedNetTotal);
    }
}
