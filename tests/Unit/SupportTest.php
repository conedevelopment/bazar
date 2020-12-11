<?php

namespace Bazar\Tests\Unit;

use ArrayIterator;
use Bazar\Bazar;
use Bazar\Support\Bags\Inventory;
use Bazar\Support\Countries;
use Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class SupportTest extends TestCase
{
    /** @test */
    public function str_has_currency_macro()
    {
        Bazar::currency('eur');
        $this->assertEquals('1,300.00 EUR', Str::currency(1300));

        Bazar::currency('usd');
        $this->assertEquals('150,300,400.00 USD', Str::currency(150300400));

        $this->assertEquals('150,300,400.00 HUF', Str::currency(150300400, 'huf'));
    }

    /** @test */
    public function support_has_country_lookup()
    {
        $this->assertCount(249, Countries::all());
        $this->assertSame('Hungary', Countries::name('HU'));
        $this->assertSame([], Countries::africa());
        $this->assertSame([], Countries::asia());
        $this->assertSame([], Countries::europe());
        $this->assertSame([], Countries::northAmerica());
        $this->assertSame([], Countries::southAmerica());
        $this->assertSame([], Countries::oceania());
    }

    /** @test */
    public function support_has_inventory_manager()
    {
        $inventory = new Inventory;

        $this->assertNull($inventory->formattedDimensions());
        $this->assertNull($inventory->formattedWeight());

        $this->assertFalse($inventory->virtual());
        $inventory->virtual = true;
        $this->assertTrue($inventory->virtual());

        $this->assertFalse($inventory->downloadable());
        $inventory->downloadable = true;
        $this->assertTrue($inventory->downloadable());

        $this->assertFalse($inventory->tracksQuantity());
        $inventory->quantity = 10;
        $this->assertTrue($inventory->tracksQuantity());

        $this->assertFalse($inventory->available(11));
        $this->assertTrue($inventory->available(8));
        $this->assertTrue($inventory->available());

        $inventory->incrementQuantity(5);
        $this->assertSame(15, (int) $inventory->quantity);
        $inventory->decrementQuantity(5);
        $this->assertSame(10, (int) $inventory->quantity);

        $inventory[] = 'test';
        $this->assertSame('test', $inventory[0]);
        unset($inventory[0]);
        $this->assertFalse(isset($inventory['fake']));
        $inventory['quantity'] = null;
        $this->assertNull($inventory['quantity']);

        $this->assertInstanceOf(ArrayIterator::class, $inventory->getIterator());

        $this->assertSame($inventory->toJson(), $inventory->__toString());
    }
}
