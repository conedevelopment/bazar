<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\AddressFactory;
use Bazar\Tests\TestCase;

class AddressesTest extends TestCase
{
    protected $address;

    public function setUp(): void
    {
        parent::setUp();

        $this->address = $this->user->addresses()->save(AddressFactory::new()->make());
    }

    /** @test */
    public function an_admin_can_index_addresses()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.users.addresses.index', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.users.addresses.index', $this->user))
            ->assertOk()
            ->assertComponent('Addresses/Index')
            ->assertPropValue('results', function ($results) {
                $this->assertEquals(
                    $results,
                    $this->user->addresses()->paginate(25)->toArray()
                );
            });
    }

    /** @test */
    public function an_admin_can_create_address()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.users.addresses.create', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.users.addresses.create', $this->user))
            ->assertOk()
            ->assertComponent('Addresses/Create');
    }

    /** @test */
    public function an_admin_can_store_address()
    {
        $this->actingAs($this->user)
            ->post(route('bazar.users.addresses.store', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)->post(
            route('bazar.users.addresses.store', $this->user),
            AddressFactory::new()->make(['first_name' => 'Test'])->toArray()
        )->assertRedirect(route('bazar.users.addresses.show', [
            $this->user,
            $this->user->fresh()->addresses->reverse()->first()
        ]));

        $this->assertDatabaseHas('addresses', ['first_name' => 'Test']);
    }

    /** @test */
    public function an_admin_can_show_address()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.users.addresses.show', [$this->user, $this->address]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.users.addresses.show', [$this->user, $this->address]))
            ->assertOk()
            ->assertComponent('Addresses/Show')
            ->assertPropValue('address', function ($address) {
                $this->assertEquals($address, $this->address->toArray());
            });
    }

    /** @test */
    public function an_admin_can_update_address()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.users.addresses.update', [$this->user, $this->address]))
            ->assertForbidden();

        $this->actingAs($this->admin)->patch(
            route('bazar.users.addresses.update', [$this->user, $this->address]),
            array_replace($this->address->toArray(), ['first_name' => 'Updated'])
        )->assertRedirect(route('bazar.users.addresses.show', [
            $this->user, $this->address
        ]));

        $this->assertSame('Updated', $this->address->refresh()->first_name);
    }

    /** @test */
    public function an_admin_can_destroy_address()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.users.addresses.destroy', [$this->user, $this->address]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.users.addresses.destroy', [$this->user, $this->address]))
            ->assertRedirect(route('bazar.users.addresses.index', $this->user));

        $this->assertDatabaseMissing('addresses', ['id' => $this->address->id]);
    }

    /** @test */
    public function an_admin_can_batch_update_addresses()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.users.addresses.batch-update', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)->patch(
            route('bazar.users.addresses.batch-update', $this->user),
            ['ids' => [$this->address->id], 'first_name' => 'Batch Update']
        )->assertStatus(302);

        $this->assertEquals('Batch Update', $this->address->fresh()->first_name);
    }

    /** @test */
    public function an_admin_can_batch_destroy_addresses()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.users.addresses.batch-destroy', $this->user))
            ->assertForbidden();

        $this->actingAs($this->admin)->delete(
            route('bazar.users.addresses.batch-destroy', $this->user),
            ['ids' => [$this->address->id]]
        )->assertStatus(302);

        $this->assertDatabaseMissing('addresses', ['id' => $this->address->id]);
    }
}
