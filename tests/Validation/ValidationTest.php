<?php

namespace Cone\Bazar\Tests\Validation;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Rules\TransactionAmount;
use Cone\Bazar\Rules\Vat;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator;

class ValidationTest extends TestCase
{
    protected Translator $translator;

    public function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->app['translator'];
    }

    public function test_validator_validatates_vat_numbers(): void
    {
        $v = new Validator($this->translator, ['vat' => 'HU12345678'], ['vat' => [new Vat()]]);
        $this->assertTrue($v->passes());

        $v = new Validator($this->translator, ['vat' => 'HU123456'], ['vat' => [new Vat()]]);
        $this->assertFalse($v->passes());
    }

    public function test_validator_validates_transaction_amounts(): void
    {
        $order = Order::factory()->create();

        $v = new Validator($this->translator, ['amount' => 0], ['amount' => [new TransactionAmount($order)]]);
        $this->assertTrue($v->passes());

        $v = new Validator($this->translator, ['amount' => 10000], ['amount' => [new TransactionAmount($order)]]);
        $this->assertFalse($v->passes());
    }
}
