<?php

namespace Bazar\Tests\Feature;

use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class WidgetsTest extends TestCase
{
    /** @test */
    public function it_shows_activities_widget()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.widgets.activities'))
            ->assertForbidden();

        Cache::shouldReceive('remember')
            ->withSomeOfArgs('bazar.activities', 3600)
            ->once()
            ->andReturn(['key' => 'value']);

        $this->actingAs($this->admin)
            ->get(route('bazar.widgets.activities'))
            ->assertOk()
            ->assertJson(['key' => 'value']);
    }

    /** @test */
    public function it_shows_metrics_widget()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.widgets.metrics'))
            ->assertForbidden();

        Cache::shouldReceive('remember')
            ->withSomeOfArgs('bazar.metrics', 3600)
            ->once()
            ->andReturn(['key' => 'value']);

        $this->actingAs($this->admin)
            ->get(route('bazar.widgets.metrics'))
            ->assertOk()
            ->assertJson(['key' => 'value']);
    }

    /** @test */
    public function it_shows_sales_widget()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.widgets.sales'))
            ->assertForbidden();

        Cache::shouldReceive('remember')
            ->withSomeOfArgs('bazar.sales', 3600)
            ->once()
            ->andReturn(['key' => 'value']);

        $this->actingAs($this->admin)
            ->get(route('bazar.widgets.sales'))
            ->assertOk()
            ->assertJson(['key' => 'value']);
    }
}
