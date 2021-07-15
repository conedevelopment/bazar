<?php

namespace Cone\Bazar\Tests\Feature;

use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class PasswordTest extends TestCase
{
    /** @test */
    public function an_admin_can_show_its_password()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.password.show'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.password.show'));
    }

    /** @test */
    public function an_admin_can_update_its_password()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.password.update'))
            ->assertForbidden();

        $this->admin->update(['password' => Hash::make('secret')]);
        $this->admin->refresh();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.password.update'), [
                'current_password' => 'not secret',
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(URL::route('bazar.password.update'), [
            'current_password' => 'secret',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(302)
          ->assertRedirect(URL::route('bazar.password.show'))
          ->assertSessionHas('message', 'Your password has been updated.');
    }
}
