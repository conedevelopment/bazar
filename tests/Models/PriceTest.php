<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Price;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class PriceTest extends TestCase
{
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
    }

    public function test_price_has_currency(): void
    {
        $price = $this->product->prices->first();

        $this->assertNotNull($price->currency);
        $this->assertIsString($price->currency);
    }

    public function test_price_has_symbol(): void
    {
        $price = $this->product->prices->first();

        $this->assertNotNull($price->symbol);
        $this->assertIsString($price->symbol);
    }

    public function test_price_formats_value(): void
    {
        $price = $this->product->prices->first();

        $formatted = $price->format();

        $this->assertIsString($formatted);
        $this->assertStringContainsString($price->symbol, $formatted);
    }

    public function test_price_can_register_custom_formatter(): void
    {
        Price::formatCurrency('USD', function ($value, $symbol, $currency) {
            return "$symbol$value $currency";
        });

        $this->product->setPrice(100, 'USD');

        $price = $this->product->prices()->where('key', 'like', '%USD%')->first();

        $formatted = $price->format();

        $this->assertStringContainsString('USD', $formatted);
    }

    public function test_price_casts_value_to_float(): void
    {
        $price = $this->product->prices->first();

        $this->assertIsFloat($price->value);
    }
}
