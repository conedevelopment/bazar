<?php

namespace Bazar\Tests\Feature;

use Bazar\Models\Order;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class WidgetsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Order::factory()->count(3)->create();
    }

    /** @test */
    public function it_shows_activities_widget()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.widgets.activities'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.widgets.activities'))
            ->assertOk()
            ->assertJson(json_decode(json_encode(Cache::get('bazar.activities')), true));
    }

    /** @test */
    public function it_shows_metrics_widget()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.widgets.metrics'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.widgets.metrics'))
            ->assertOk()
            ->assertJson((array) Cache::get('bazar.activities'));
    }

    /** @test */
    public function it_shows_sales_widget()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.widgets.sales'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.widgets.sales'))
            ->assertOk()
            ->assertJson((array) Cache::get('bazar.activities'));
    }
}
