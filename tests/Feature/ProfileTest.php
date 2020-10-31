<?php

namespace Bazar\Tests\Feature;

use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class ProfileTest extends TestCase
{
    /** @test */
    public function an_admin_can_show_its_profile()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.profile.show'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.profile.show'))
            ->assertViewHas('page.props.action', URL::route('bazar.profile.update'));
    }

    /** @test */
    public function an_admin_can_update_its_profile()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.profile.update'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.profile.update'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(URL::route('bazar.profile.update'), [
            'name' => 'Updated',
            'email' => 'updated@example.test',
        ])->assertStatus(302)
          ->assertRedirect(URL::route('bazar.profile.show'))
          ->assertSessionHas('message', 'Your profile has been updated.');

        $this->assertDatabaseHas('users', [
            'name' => 'Updated',
            'email' => 'updated@example.test',
        ]);
    }
}
