<?php

namespace Bazar\Tests\Unit;

use Bazar\Bazar;
use Bazar\Exceptions\InvalidCurrencyException;
use Bazar\Models\Product;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class BazarTest extends TestCase
{
    /** @test */
    public function bazar_has_version()
    {
        $this->assertSame(Bazar::VERSION, Bazar::getVersion());
    }

    /** @test */
    public function bazar_has_currencies()
    {
        $this->assertSame(
            $this->app['config']->get('bazar.currencies.available'),
            array_flip(Bazar::getCurrencies())
        );
    }

    /** @test */
    public function bazar_can_get_currency()
    {
        $this->assertSame($this->app['config']->get('bazar.currencies.default'), Bazar::getCurrency());
    }

    /** @test */
    public function bazar_can_set_currency()
    {
        Bazar::setCurrency('eur');
        $this->assertSame('eur', Bazar::getCurrency());

        $this->expectException(InvalidCurrencyException::class);
        Bazar::setCurrency('fake');
    }

    /** @test */
    public function bazar_can_route_bind_soft_deleted_only_for_bazar_routes()
    {
        Route::middleware('web')->get('shop/products/{product}', function (Product $product) {
            //
        });

        $product = Product::factory()->create();

        $this->get('shop/products/'.$product->id)->assertOk();

        $product->delete();

        $this->get('shop/products/'.$product->id)->assertNotFound();
    }
}
