<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Taxable;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Models\Item;
use Bazar\Support\Facades\Cart;
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

        $product = ProductFactory::new()->create();

        $this->item = Cart::add($product, 3, ['text' => 'test-text']);
    }

    /** @test */
    public function it_has_attributes()
    {
        $this->assertSame($this->item->product->price('sale') + 0.9, $this->item->price);
        $this->assertSame($this->item->price, $this->item->price());
        $this->assertSame(
            Str::currency($this->item->price, $this->item->pivotParent->currency), $this->item->formattedPrice()
        );
        $this->assertSame($this->item->formattedPrice(), $this->item->formattedPrice);
        $this->assertSame($this->item->price * 0.1, $this->item->tax);
        $this->assertSame(
            Str::currency($this->item->tax, $this->item->pivotParent->currency),
            $this->item->formattedTax()
        );
        $this->assertSame(
            ($this->item->price + $this->item->tax) * $this->item->quantity,
            $this->item->total()
        );
        $this->assertSame($this->item->total(), $this->item->total);
        $this->assertSame(
            Str::currency($this->item->total, $this->item->pivotParent->currency),
            $this->item->formattedTotal()
        );
        $this->assertSame($this->item->formattedTotal(), $this->item->formattedTotal);
        $this->assertSame($this->item->price * $this->item->quantity, $this->item->netTotal());
        $this->assertSame($this->item->netTotal(), $this->item->netTotal);
        $this->assertSame(
            Str::currency($this->item->netTotal, $this->item->pivotParent->currency),
            $this->item->formattedNetTotal()
        );
        $this->assertSame($this->item->formattedNetTotal(), $this->item->formattedNetTotal);
    }

    /** @test */
    public function it_accesses_its_properties()
    {
        $this->assertSame('test-text', $this->item->property('text'));
        $this->assertNull($this->item->property('fake'));
        $this->assertSame('test-text', $this->item->property('fake', 'test-text'));
    }
}
