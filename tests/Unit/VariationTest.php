<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariationFactory;
use Bazar\Tests\TestCase;

class VariationTest extends TestCase
{
    protected $variation, $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = ProductFactory::new()->create();

        $this->variation = VariationFactory::new()->make();
        $this->variation->product()->associate($this->product);
        $this->variation->save();
    }

    /** @test */
    public function a_variation_belongs_to_a_product()
    {
        $this->assertEquals($this->product->id, $this->variation->product_id);
    }
}
