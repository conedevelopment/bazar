<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class OrdersTest extends TestCase
{
    protected $order;

    public function setUp(): void
    {
        parent::setUp();

        ProductFactory::new()->create();

        $this->order = $this->admin->orders()->save(OrderFactory::new()->make());
        $this->order->address()->save(AddressFactory::new()->make());
        $this->order->shipping->save();
        $this->order->shipping->address()->save(AddressFactory::new()->make());
    }

    /** @test */
    public function an_admin_can_index_orders()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.orders.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.orders.index'))
            ->assertOk()
            ->assertViewHas(
                'page.props.results',
                Order::query()->with(['address', 'products', 'transactions', 'shipping'])->paginate()->toArray()
            );
    }

    /** @test */
    public function an_admin_can_create_order()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.orders.create'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.orders.create'))
            ->assertOk()
            ->assertViewHas('page.props.order');
    }

    /** @test */
    public function an_admin_can_store_order()
    {
        $this->actingAs($this->user)
            ->post(URL::route('bazar.orders.store'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(URL::route('bazar.orders.store'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $order = OrderFactory::new()->make();
        $order->setRelation('address', AddressFactory::new()->make());
        $order->shipping->setRelation('address', AddressFactory::new()->make());

        $product = Product::first();

        $this->actingAs($this->admin)->post(
            URL::route('bazar.orders.store'),
            array_merge($order->toArray(), ['products' => [
                $product->toArray() + ['item' => ['price' => $product->price, 'quantity' => 1, 'tax' => 10]],
            ]])
        )->assertRedirect(URL::route('bazar.orders.show', Order::find(2)));

        $this->assertDatabaseHas('bazar_orders', $order->only(['discount', 'currency']));
    }

    /** @test */
    public function an_admin_can_show_order()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.orders.show', $this->order))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.orders.show', $this->order))
            ->assertOk()
            ->assertViewHas(
                'page.props.order',
                $this->order->refresh()->loadMissing([
                    'address', 'products', 'transactions', 'shipping', 'shipping.address',
                ])->toArray()
            );
    }

    /** @test */
    public function an_admin_can_update_order()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.orders.update', $this->order))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.orders.update', $this->order), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->order->loadMissing(['address', 'shipping', 'shipping.address']);

        $this->actingAs($this->admin)->patch(
            URL::route('bazar.orders.update', $this->order),
            array_replace($this->order->toArray(), ['status' => 'cancelled'])
        )->assertRedirect(URL::route('bazar.orders.show', $this->order));

        $this->assertSame('cancelled', $this->order->refresh()->status);
    }

    /** @test */
    public function an_admin_can_destroy_order()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.orders.destroy', $this->order))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.orders.destroy', $this->order))
            ->assertStatus(302);

        $this->assertTrue($this->order->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.orders.destroy', $this->order))
            ->assertStatus(302);

        $this->assertDatabaseMissing('bazar_orders', ['id' => $this->order->id]);
    }

    /** @test */
    public function an_admin_can_restore_order()
    {
        $this->order->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.orders.restore', $this->order))
            ->assertForbidden();

        $this->assertTrue($this->order->trashed());

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.orders.restore', $this->order))
            ->assertStatus(302);

        $this->assertFalse($this->order->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_orders()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.orders.batch-update'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.orders.batch-update'), ['ids' => [$this->order->id], 'status' => 'cancelled'])
            ->assertStatus(302);

        $this->assertEquals('cancelled', $this->order->fresh()->status);
    }

    /** @test */
    public function an_admin_can_batch_destroy_orders()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.orders.batch-destroy'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.orders.batch-destroy'), ['ids' => [$this->order->id]])
            ->assertStatus(302);

        $this->assertTrue($this->order->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.orders.batch-destroy', ['force']), ['ids' => [$this->order->id]])
            ->assertStatus(302);

        $this->assertDatabaseMissing('bazar_orders', ['id' => $this->order->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_orders()
    {
        $this->order->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.orders.batch-restore'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.orders.batch-restore'), ['ids' => [$this->order->id]])
            ->assertStatus(302);

        $this->assertFalse($this->order->fresh()->trashed());
    }
}
