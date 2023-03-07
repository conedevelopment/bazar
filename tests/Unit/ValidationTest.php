<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Property;
use Cone\Bazar\Models\Variant;
use Cone\Bazar\Rules\Option;
use Cone\Bazar\Rules\TransactionAmount;
use Cone\Bazar\Rules\Vat;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Validation\Validator;

class ValidationTest extends TestCase
{
    protected $translator;

    public function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->app['translator'];
    }

    /** @test */
    public function it_validatates_vat_numbers()
    {
        $v = new Validator($this->translator, ['vat' => 'HU12345678'], ['vat' => [new Vat()]]);
        $this->assertTrue($v->passes());

        $v = new Validator($this->translator, ['vat' => 'HU123456'], ['vat' => [new Vat()]]);
        $this->assertFalse($v->passes());
    }

    /** @test */
    public function it_validates_transaction_amounts()
    {
        $order = Order::factory()->create();

        $v = new Validator($this->translator, ['amount' => 0], ['amount' => [new TransactionAmount($order)]]);
        $this->assertTrue($v->passes());

        $v = new Validator($this->translator, ['amount' => 10000], ['amount' => [new TransactionAmount($order)]]);
        $this->assertFalse($v->passes());
    }

    /** @test */
    public function it_validates_variant_options()
    {
        $property = Property::factory()->create(['name' => 'Material', 'slug' => 'material']);
        $property->values()->create(['name' => 'Gold', 'value' => 'gold']);

        $product = Product::factory()->create();
        $product->propertyValues()->attach($property->values);

        $variant = $product->variants()->save(Variant::factory()->make());
        $variant->propertyValues()->attach($property->values);

        $v = new Validator(
            $this->translator,
            ['variation' => ['material' => 'silver']],
            ['variation' => [new Option($product)]]
        );
        $this->assertTrue($v->passes());

        $v = new Validator(
            $this->translator,
            ['variation' => ['material' => 'gold']],
            ['variation' => [new Option($product)]]
        );
        $this->assertFalse($v->passes());

        $v = new Validator(
            $this->translator,
            ['variation' => ['material' => 'gold']],
            ['variation' => [new Option($product, $variant)]]
        );
        $this->assertTrue($v->passes());
    }
}
