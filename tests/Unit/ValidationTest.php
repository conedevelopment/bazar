<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariationFactory;
use Bazar\Rules\Option;
use Bazar\Rules\TransactionAmount;
use Bazar\Rules\Vat;
use Bazar\Tests\TestCase;
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
        $v = new Validator($this->translator, ['vat' => 'HU12345678'], ['vat' => [new Vat]]);
        $this->assertTrue($v->passes());

        $v = new Validator($this->translator, ['vat' => 'HU123456'], ['vat' => [new Vat]]);
        $this->assertFalse($v->passes());
    }

    /** @test */
    public function it_validates_transaction_amounts()
    {
        $order = OrderFactory::new()->create();

        $v = new Validator($this->translator, ['amount' => 0], ['amount' => [new TransactionAmount($order)]]);
        $this->assertTrue($v->passes());

        $v = new Validator($this->translator, ['amount' => 10000], ['amount' => [new TransactionAmount($order)]]);
        $this->assertFalse($v->passes());
    }

    /** @test */
    public function it_validates_variation_options()
    {
        $product = ProductFactory::new()->create();
        $variation = $product->variations()->save(
            VariationFactory::new()->make()
        );

        $v = new Validator(
            $this->translator,
            ['option' => ['Material' => 'Gold']],
            ['option' => [new Option($product)]]
        );
        $this->assertTrue($v->passes());

        $v = new Validator(
            $this->translator,
            ['option' => $variation->option],
            ['option' => [new Option($product)]]
        );
        $this->assertFalse($v->passes());

        $v = new Validator(
            $this->translator,
            ['option' => $variation->option],
            ['option' => [new Option($product, $variation)]]
        );
        $this->assertTrue($v->passes());
    }
}
