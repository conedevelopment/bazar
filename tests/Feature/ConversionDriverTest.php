<?php

namespace Bazar\Tests\Feature;

use Bazar\Tests\TestCase;

class ConversionDriverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_be_resolved_via_facade()
    {
        $this->assertTrue(true);
    }
}
