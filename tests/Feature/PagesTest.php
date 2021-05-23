<?php

namespace Bazar\Tests\Feature;

use Bazar\Bazar;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class PagesTest extends TestCase
{
    /** @test */
    public function an_admin_can_view_dashboard_page()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.dashboard'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.dashboard'));
    }

    /** @test */
    public function an_admin_can_view_support_page()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.support'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.support'))
            ->assertOk()
            ->assertViewHas('page.props.version', Bazar::getVersion());
    }
}
