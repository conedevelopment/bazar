<?php

namespace Bazar\Tests\Feature;

use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\Transaction;
use Bazar\Tests\TestCase;

class TransactionsTest extends TestCase
{
    protected $order, $transaction;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'X-Bazar' => true,
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->order = $this->admin->orders()->save(factory(Order::class)->make());

        $this->transaction = $this->order->transactions()->save(factory(Transaction::class)->make([
            'amount' => 0,
            'type' => 'payment',
        ]));
    }

    /** @test */
    public function an_admin_can_store_transaction()
    {
        $this->order->products()->attach(
            $product = factory(Product::class)->create(),
            ['quantity' => 1, 'tax' => 0, 'price' => $product->price],
        );

        $this->actingAs($this->user)
            ->post(route('bazar.orders.transactions.store', $this->order))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(route('bazar.orders.transactions.store', $this->order), [])
            ->assertStatus(422);

        $this->actingAs($this->admin)->post(
            route('bazar.orders.transactions.store', $this->order),
            $t = factory(Transaction::class)->make([
                'type' => 'payment',
                'driver' => 'manual',
                'amount' => $this->order->totalPayable(),
            ])->toArray()
        )->assertOk()
         ->assertJson($t);

        $this->assertDatabaseHas('transactions', ['amount' => $t['amount'], 'type' => $t['type']]);
    }

    /** @test */
    public function an_admin_can_update_transaction()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.orders.transactions.update', [$this->order, $this->transaction]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.orders.transactions.update', [$this->order, $this->transaction]))
            ->assertOk()
            ->assertExactJson(['updated' => true]);
    }

    /** @test */
    public function an_admin_can_destroy_transaction()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.orders.transactions.destroy', [$this->order, $this->transaction]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.orders.transactions.destroy', [$this->order, $this->transaction]))
            ->assertOk()
            ->assertExactJson(['deleted' => true]);

        $this->assertDatabaseMissing('transactions', ['id' => $this->transaction->id]);
    }
}
