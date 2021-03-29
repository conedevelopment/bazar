<?php

namespace Bazar\Tests\Feature;

use Bazar\Models\User;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class UsersTest extends TestCase
{
    /** @test */
    public function an_admin_can_index_users()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.users.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.users.index'))
            ->assertOk()
            ->assertViewHas(
                'page.props.results', User::with('addresses')->paginate()->toArray()
            );
    }

    /** @test */
    public function an_admin_can_create_user()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.users.create'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.users.create'))
            ->assertOk()
            ->assertViewHas('page.props.user');
    }

    /** @test */
    public function an_admin_can_store_user()
    {
        $this->actingAs($this->user)
            ->post(URL::route('bazar.users.store'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(URL::route('bazar.users.store'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(
            URL::route('bazar.users.store'),
            User::factory()->make(['name' => 'Test'])->toArray()
        )->assertRedirect(URL::route('bazar.users.show', User::find(3)))
         ->assertSessionHas('message', 'The user has been created.');

        $this->assertDatabaseHas('users', ['name' => 'Test']);
    }

    /** @test */
    public function an_admin_can_show_user()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.users.show', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.users.show', $this->user))
            ->assertOk()
            ->assertViewHas('page.props.user', $this->user->refresh()->toArray());
    }

    /** @test */
    public function an_admin_can_update_user()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.users.update', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.users.update', $this->user), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(
            URL::route('bazar.users.update', $this->user),
            array_replace($this->user->toArray(), ['name' => 'Updated'])
        )->assertRedirect(URL::route('bazar.users.show', $this->user))
         ->assertSessionHas('message', 'The user has been updated.');

        $this->assertSame('Updated', $this->user->refresh()->name);
    }

    /** @test */
    public function an_admin_can_destroy_user()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.users.destroy', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.users.destroy', $this->admin))
            ->assertStatus(302)
            ->assertSessionHas('message', 'The authenticated user cannot be deleted.');

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.users.destroy', $this->user))
            ->assertStatus(302)
            ->assertSessionHas('message', 'The user has been deleted.');

        $this->assertTrue($this->user->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.users.destroy', $this->user))
            ->assertStatus(302)
            ->assertSessionHas('message', 'The user has been deleted.');

        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    /** @test */
    public function an_admin_can_restore_user()
    {
        $this->user->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.users.restore', $this->user))
            ->assertForbidden();

        $this->assertTrue($this->user->trashed());

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.users.restore', $this->user))
            ->assertStatus(302);

        $this->assertFalse($this->user->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_users()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.users.batch-update'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.users.batch-update'), ['id' => [$this->user->id], 'name' => 'Cat'])
            ->assertStatus(302);

        $this->assertEquals('Cat', $this->user->fresh()->name);
    }

    /** @test */
    public function an_admin_can_batch_destroy_users()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.users.batch-destroy'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.users.batch-destroy'), ['id' => [$this->user->id]])
            ->assertStatus(302);

        $this->assertTrue($this->user->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.users.batch-destroy', ['force']), ['id' => [$this->user->id]])
            ->assertStatus(302);

        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_users()
    {
        $this->user->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.users.batch-restore'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.users.batch-restore'), ['id' => [$this->user->id]])
            ->assertStatus(302);

        $this->assertFalse($this->user->fresh()->trashed());
    }
}
