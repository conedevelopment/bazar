<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Relations;

use Cone\Bazar\Models\Price;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class PricesTest extends TestCase
{
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
    }

    public function test_prices_relation_returns_only_price_meta(): void
    {
        $this->product->setPrice(100);

        $prices = $this->product->prices;

        $this->assertNotEmpty($prices);

        foreach ($prices as $price) {
            $this->assertInstanceOf(Price::class, $price);
            $this->assertStringStartsWith('price_', $price->key);
        }
    }

    public function test_prices_relation_filters_non_price_meta(): void
    {
        $this->product->setMeta('custom_key', 'value');
        $this->product->setPrice(100);

        $prices = $this->product->prices;

        $this->assertNotEmpty($prices);

        foreach ($prices as $price) {
            $this->assertStringStartsWith('price_', $price->key);
        }
    }

    public function test_prices_relation_handles_multiple_currencies(): void
    {
        $this->product->setPrice(100, 'USD');
        $this->product->setPrice(90, 'EUR');

        $prices = $this->product->prices;

        $this->assertGreaterThanOrEqual(2, $prices->count());

        $usdPrice = $prices->firstWhere('key', 'like', '%USD%');
        $this->assertNotNull($usdPrice);

        $eurPrice = $prices->firstWhere('key', 'like', '%EUR%');
        $this->assertNotNull($eurPrice);
    }
}
