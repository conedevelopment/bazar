<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\TransactionFactory;
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

        $this->order = $this->admin->orders()->save(OrderFactory::new()->make());

        $this->transaction = $this->order->transactions()->save(TransactionFactory::new()->make([
            'amount' => 0,
            'type' => 'payment',
        ]));
    }

    /** @test */
    public function an_admin_can_store_transaction()
    {
        $this->order->products()->attach(
            $product = ProductFactory::new()->create(),
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
            $payment = TransactionFactory::new()->make([
                'type' => 'payment',
                'driver' => 'manual',
                'amount' => $this->order->fresh()->totalPayable(),
            ])->toArray()
        )->assertOk()
         ->assertJson($payment);

         $this->actingAs($this->admin)->post(
            route('bazar.orders.transactions.store', $this->order),
            $refund = TransactionFactory::new()->make([
                'type' => 'refund',
                'driver' => 'manual',
                'amount' => $this->order->totalRefundable(),
            ])->toArray()
        )->assertOk()
         ->assertJson($refund);

        $this->assertDatabaseHas('transactions', ['amount' => $payment['amount'], 'type' => $payment['type']]);
        $this->assertDatabaseHas('transactions', ['amount' => $refund['amount'], 'type' => $refund['type']]);
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
