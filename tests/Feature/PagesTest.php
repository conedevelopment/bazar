<?php

namespace Bazar\Tests\Feature;

use Bazar\Tests\TestCase;

class PagesTest extends TestCase
{
    /** @test */
    public function an_admin_can_view_dashboard_page()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.dashboard'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.dashboard'))
            ->assertOk()
            ->assertComponent('Dashboard');
    }

    /** @test */
    public function an_admin_can_view_support_page()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.support'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.support'))
            ->assertOk()
            ->assertComponent('Support');
    }
}
