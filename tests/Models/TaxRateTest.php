<?php

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Models\TaxRate;
use Cone\Bazar\Tests\TestCase;

class TaxRateTest extends TestCase
{
    protected TaxRate $taxRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taxRate = TaxRate::factory()->create();
    }

    public function test_a_tax_rate_calculates_tax_value(): void
    {
        $taxable = Shipping::factory()->make();

        $this->assertSame(
            round($taxable->getTaxBase() * $this->taxRate->rate, 2),
            $this->taxRate->calculate($taxable)
        );
    }

    public function test_a_tax_rate_has_query_scopes(): void
    {
        $this->assertSame(
            $this->taxRate->newQuery()->where('bazar_tax_rates.shipping', true)->toSql(),
            $this->taxRate->newQuery()->applicableForShipping()->toSql()
        );
    }
}
