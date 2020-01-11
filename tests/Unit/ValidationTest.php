<?php

namespace Bazar\Tests\Unit;

use Bazar\Rules\Vat;
use Bazar\Tests\TestCase;
use Illuminate\Validation\Validator;

class ValidationTest extends TestCase
{
    protected $translator;

    public function setUp(): void
    {
        parent::setUp();

        $this->trans = $this->app['translator'];
    }

    /** @test */
    public function it_validatates_vat_numbers()
    {
        $v = new Validator($this->trans, ['vat' => 'HU12345678'], ['vat' => [new Vat]]);
        $this->assertTrue($v->passes());

        $v = new Validator($this->trans, ['vat' => 'HU123456'], ['vat' => [new Vat]]);
        $this->assertFalse($v->passes());
    }
}
