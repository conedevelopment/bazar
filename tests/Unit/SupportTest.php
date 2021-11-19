<?php

namespace Cone\Bazar\Tests\Unit;

use ArrayIterator;
use Cone\Bazar\Bazar;
use Cone\Bazar\Casts\Inventory;
use Cone\Root\Support\Countries;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class SupportTest extends TestCase
{
    /** @test */
    public function str_has_currency_macro()
    {
        Bazar::setCurrency('eur');
        $this->assertEquals('1,300.00 EUR', Str::currency(1300));

        Bazar::setCurrency('usd');
        $this->assertEquals('150,300,400.00 USD', Str::currency(150300400));

        $this->assertEquals('150,300,400.00 HUF', Str::currency(150300400, 'huf'));
    }

    /** @test */
    public function support_has_country_lookup()
    {
        $this->assertCount(58, Countries::africa());
        $this->assertCount(5, Countries::antarctica());
        $this->assertCount(54, Countries::asia());
        $this->assertCount(51, Countries::europe());
        $this->assertCount(41, Countries::northAmerica());
        $this->assertCount(14, Countries::southAmerica());
        $this->assertCount(26, Countries::oceania());
        $this->assertCount(249, Countries::all());
        $this->assertSame('Hungary', Countries::name('HU'));
    }

    /** @test */
    public function support_has_inventory_manager()
    {
        $inventory = new Inventory();

        $this->assertNull($inventory->getFormattedDimensions());
        $this->assertNull($inventory->getFormattedWeight());

        $this->assertFalse($inventory->virtual());
        $inventory['virtual'] = true;
        $this->assertTrue($inventory->virtual());

        $this->assertFalse($inventory->downloadable());
        $inventory['downloadable'] = true;
        $this->assertTrue($inventory->downloadable());

        $this->assertFalse($inventory->tracksQuantity());
        $inventory['quantity'] = 10;
        $this->assertTrue($inventory->tracksQuantity());

        $this->assertFalse($inventory->available(11));
        $this->assertTrue($inventory->available(8));
        $this->assertTrue($inventory->available());

        $inventory->incrementQuantity(5);
        $this->assertSame(15, (int) $inventory['quantity']);
        $inventory->decrementQuantity(5);
        $this->assertSame(10, (int) $inventory['quantity']);

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
