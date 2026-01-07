<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Traits;

use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class HasPricesTest extends TestCase
{
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
    }

    public function test_model_has_prices(): void
    {
        $this->assertNotEmpty($this->product->prices);
    }

    public function test_model_gets_price(): void
    {
        $price = $this->product->getPrice();

        $this->assertIsFloat($price);
        $this->assertGreaterThan(0, $price);
    }

    public function test_model_gets_formatted_price(): void
    {
        $formatted = $this->product->getFormattedPrice();

        $this->assertIsString($formatted);
    }

    public function test_model_price_attribute(): void
    {
        $this->assertIsFloat($this->product->price);
        $this->assertEquals($this->product->getPrice(), $this->product->price);
    }

    public function test_model_formatted_price_attribute(): void
    {
        $this->assertIsString($this->product->formatted_price);
        $this->assertEquals($this->product->getFormattedPrice(), $this->product->formatted_price);
    }

    public function test_model_gets_price_html(): void
    {
        $html = $this->product->getPriceHtml();

        $this->assertIsString($html->toHtml());
    }

    public function test_model_determines_if_free(): void
    {
        $isFree = $this->product->isFree();

        $this->assertIsBool($isFree);
    }

    public function test_free_product_shows_free_html(): void
    {
        $this->product->prices()->delete();

        $html = $this->product->getPriceHtml();

        $this->assertStringContainsString('Free', $html->toHtml());
    }
}
