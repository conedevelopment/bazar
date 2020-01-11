<?php

namespace Bazar\Tests\Feature;

use Bazar\Models\User;
use Bazar\Tests\TestCase;

class UsersTest extends TestCase
{
    /** @test */
    public function an_admin_can_index_users()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.users.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.users.index'))
            ->assertOk()
            ->assertComponent('Users/Index')
            ->assertPropValue('results', function ($results) {
                $this->assertEquals(
                    $results, User::with('addresses')->paginate(25)->toArray()
                );
            });
    }

    /** @test */
    public function an_admin_can_create_user()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.users.create'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.users.create'))
            ->assertOk()
            ->assertComponent('Users/Create');
    }

    /** @test */
    public function an_admin_can_store_user()
    {
        $this->actingAs($this->user)
            ->post(route('bazar.users.store'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(route('bazar.users.store'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(
            route('bazar.users.store'),
            factory(User::class)->make(['name' => 'Test'])->toArray()
        )->assertRedirect(route('bazar.users.show', User::find(3)));

        $this->assertDatabaseHas('users', ['name' => 'Test']);
    }

    /** @test */
    public function an_admin_can_show_user()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.users.show', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.users.show', $this->user))
            ->assertOk()
            ->assertComponent('Users/Show')
            ->assertPropValue('user', function ($user) {
                $this->assertEquals($user, $this->user->refresh()->toArray());
            });
    }

    /** @test */
    public function an_admin_can_update_user()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.users.update', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.users.update', $this->user), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(
            route('bazar.users.update', $this->user),
            array_replace($this->user->toArray(), ['name' => 'Updated'])
        )->assertRedirect(route('bazar.users.show', $this->user));

        $this->assertSame('Updated', $this->user->refresh()->name);
    }

    /** @test */
    public function an_admin_can_destroy_user()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.users.destroy', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.users.destroy', $this->user))
            ->assertStatus(302);

        $this->assertTrue($this->user->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(route('bazar.users.destroy', $this->user))
            ->assertStatus(302);

        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    /** @test */
    public function an_admin_can_restore_user()
    {
        $this->user->delete();

        $this->actingAs($this->user)
            ->patch(route('bazar.users.restore', $this->user))
            ->assertForbidden();

        $this->assertTrue($this->user->trashed());

        $this->actingAs($this->admin)
            ->patch(route('bazar.users.restore', $this->user))
            ->assertStatus(302);

        $this->assertFalse($this->user->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_users()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.users.batch-update'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.users.batch-update'), ['ids' => [$this->user->id], 'name' => 'Cat'])
            ->assertStatus(302);

        $this->assertEquals('Cat', $this->user->fresh()->name);
    }

    /** @test */
    public function an_admin_can_batch_destroy_users()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.users.batch-destroy'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.users.batch-destroy'), ['ids' => [$this->user->id]])
            ->assertStatus(302);

        $this->assertTrue($this->user->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(route('bazar.users.batch-destroy', ['force']), ['ids' => [$this->user->id]])
            ->assertStatus(302);

        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_users()
    {
        $this->user->delete();

        $this->actingAs($this->user)
            ->patch(route('bazar.users.batch-restore'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.users.batch-restore'), ['ids' => [$this->user->id]])
            ->assertStatus(302);

        $this->assertFalse($this->user->fresh()->trashed());
    }
}
