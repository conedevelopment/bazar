<?php

namespace Bazar\Tests\Unit;

use Bazar\Casts\Inventory;
use Bazar\Casts\Prices;
use Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class AttributeBagTest extends TestCase
{
    /** @test */
    public function it_handles_inventory_bag()
    {
        $inventory = new Inventory([
            'files' => [],
            'sku' => Str::random(5),
            'quantity' => 20,
            'weight' => 200,
            'virtual' => false,
            'downloadable' => false,
            'length' => 200,
            'width' => 300,
            'height' => 400,
        ]);

        $this->assertSame(
            sprintf('%s mm', implode('x', [$inventory->length, $inventory->width, $inventory->height])),
            $inventory->formattedDimensions('x')
        );
        $this->assertNull((new Inventory)->formattedDimensions());

        $this->assertSame(sprintf('%s g', $inventory['weight']), $inventory->formattedWeight());
        $this->assertNull((new Inventory)->formattedWeight());

        $this->assertTrue($inventory->tracksQuantity());
        $this->assertTrue($inventory->available());
        $this->assertFalse($inventory->available(600));
        $this->assertSame(20, $inventory->quantity);
        $inventory->incrementQuantity(10);
        $this->assertSame(30, (int) $inventory->quantity);
        $inventory->decrementQuantity(6);
        $this->assertSame(24, (int) $inventory->quantity);

        $this->assertFalse($inventory->virtual());
        $this->assertFalse($inventory->downloadable());

        $inventory->foo = 'bar';
        $inventory->bar = 'foo';
        $this->assertTrue(isset($inventory->foo, $inventory->bar));
        unset($inventory->foo);
        $this->assertFalse(isset($inventory->foo));
    }

    /** @test */
    public function it_handles_prices_bag()
    {
        $prices = new Prices([
            'usd' => [
                'default' => 100,
                'sale' => 80,
            ],
        ]);

        $this->assertSame(100, $prices->usd['default']);
        $this->assertSame(80, $prices->usd['sale']);
        $this->assertSame('100.00 USD', $prices->format());
    }
}
