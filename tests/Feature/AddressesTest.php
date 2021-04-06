<?php

namespace Bazar\Tests\Feature;

use Bazar\Models\Address;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class AddressesTest extends TestCase
{
    protected $address;

    public function setUp(): void
    {
        parent::setUp();

        $this->address = $this->user->addresses()->save(
            Address::factory()->make()
        );
    }

    /** @test */
    public function an_admin_can_index_addresses()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.users.addresses.index', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.users.addresses.index', $this->user))
            ->assertOk()
            ->assertViewHas(
                'page.props.response', $this->user->addresses()->paginate()->toArray()
            );
    }

    /** @test */
    public function an_admin_can_create_address()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.users.addresses.create', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.users.addresses.create', $this->user))
            ->assertOk()
            ->assertViewHas('page.props.address');
    }

    /** @test */
    public function an_admin_can_store_address()
    {
        $this->actingAs($this->user)
            ->post(URL::route('bazar.users.addresses.store', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)->post(
            URL::route('bazar.users.addresses.store', $this->user),
            Address::factory()->make(['first_name' => 'Test'])->toArray()
        )->assertRedirect(URL::route('bazar.users.addresses.show', [
            $this->user,
            $this->user->fresh()->addresses->reverse()->first()
        ]));

        $this->assertDatabaseHas('bazar_addresses', ['first_name' => 'Test']);
    }

    /** @test */
    public function an_admin_can_show_address()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.users.addresses.show', [$this->user, $this->address]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.users.addresses.show', [$this->user, $this->address]))
            ->assertOk()
            ->assertViewHas('page.props.address', $this->address->toArray());
    }

    /** @test */
    public function an_admin_can_update_address()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.users.addresses.update', [$this->user, $this->address]))
            ->assertForbidden();

        $this->actingAs($this->admin)->patch(
            URL::route('bazar.users.addresses.update', [$this->user, $this->address]),
            array_replace($this->address->toArray(), ['first_name' => 'Updated'])
        )->assertRedirect(URL::route('bazar.users.addresses.show', [
            $this->user, $this->address
        ]));

        $this->assertSame('Updated', $this->address->refresh()->first_name);
    }

    /** @test */
    public function an_admin_can_destroy_address()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.users.addresses.destroy', [$this->user, $this->address]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.users.addresses.destroy', [$this->user, $this->address]))
            ->assertRedirect(URL::route('bazar.users.addresses.index', $this->user));

        $this->assertDatabaseMissing('bazar_addresses', ['id' => $this->address->id]);
    }

    /** @test */
    public function an_admin_can_batch_update_addresses()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.users.addresses.batch-update', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)->patch(
            URL::route('bazar.users.addresses.batch-update', $this->user),
            ['id' => [$this->address->id], 'first_name' => 'Batch Update']
        )->assertStatus(302);

        $this->assertEquals('Batch Update', $this->address->fresh()->first_name);
    }

    /** @test */
    public function an_admin_can_batch_destroy_addresses()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.users.addresses.batch-destroy', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)->delete(
            URL::route('bazar.users.addresses.batch-destroy', $this->user),
            ['id' => [$this->address->id]]
        )->assertStatus(302);

        $this->assertDatabaseMissing('bazar_addresses', ['id' => $this->address->id]);
    }
}
