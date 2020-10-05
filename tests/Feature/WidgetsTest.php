<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\OrderFactory;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class WidgetsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        OrderFactory::new()->count(3)->create();
    }

    /** @test */
    public function it_shows_activities_widget()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.widgets.activities'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.widgets.activities'))
            ->assertOk()
            ->assertJson(json_decode(json_encode(Cache::get('bazar.activities')), true));
    }

    /** @test */
    public function it_shows_metrics_widget()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.widgets.metrics'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.widgets.metrics'))
            ->assertOk()
            ->assertJson((array) Cache::get('bazar.activities'));
    }

    /** @test */
    public function it_shows_sales_widget()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.widgets.sales'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.widgets.sales'))
            ->assertOk()
            ->assertJson((array) Cache::get('bazar.activities'));
    }
}
