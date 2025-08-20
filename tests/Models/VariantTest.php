<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\TaxRate;
use Cone\Bazar\Models\Variant;
use Cone\Bazar\Tests\TestCase;

class VariantTest extends TestCase
{
    protected Variant $variant;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();

        $this->variant = Variant::factory()->make();
        $this->variant->product()->associate($this->product);
        $this->variant->save();
    }

    public function test_variant_belongs_to_a_product(): void
    {
        $this->assertEquals($this->product->id, $this->variant->product_id);
    }

    public function test_a_product_belongs_to_tax_rates(): void
    {
        $taxRate = TaxRate::factory()->create();

        $this->assertTrue($this->product->taxRates->isEmpty());

        $this->product->taxRates()->attach($taxRate);

        $this->product->refresh();

        $this->assertTrue($this->product->taxRates->contains($taxRate));
    }

    public function test_variant_has_alias_attribute(): void
    {
        $variant = Variant::factory()->make(['alias' => 'Fake']);

        $this->assertSame('Fake', $variant->alias);

        $variant->alias = null;
        $variant->product()->associate($this->product);
        $variant->save();

        $this->assertSame("#{$variant->id}", $variant->alias);
    }
}
