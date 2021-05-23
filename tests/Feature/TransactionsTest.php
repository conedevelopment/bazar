<?php

namespace Bazar\Tests\Feature;

use Bazar\Exceptions\TransactionFailedException;
use Bazar\Gateway\Driver;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\Transaction;
use Bazar\Support\Facades\Gateway;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

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

        $this->order = $this->admin->orders()->save(Order::factory()->make());

        $this->transaction = $this->order->transactions()->save(Transaction::factory()->make([
            'amount' => 0,
            'type' => 'payment',
        ]));

        Gateway::extend('fake', function ($app) {
            return new FakeGateway();
        });
    }

    /** @test */
    public function an_admin_can_store_transaction()
    {
        $this->order->transactions()->save(
            Transaction::factory()->make(['driver' => 'fake', 'amount' => 0])
        );

        $product = Product::factory()->create();
        $this->order->items()->create([
            'buyable_id' => $product->id,
            'buyable_type' => Product::class,
            'quantity' => 1,
            'tax' => 0,
            'price' => $product->price,
        ]);

        $this->actingAs($this->user)
            ->post(URL::route('bazar.orders.transactions.store', $this->order))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(URL::route('bazar.orders.transactions.store', $this->order), [])
            ->assertStatus(422);

        $this->actingAs($this->admin)->post(
            URL::route('bazar.orders.transactions.store', $this->order),
            $payment = Transaction::factory()->make([
                'type' => 'refund',
                'driver' => 'fake',
                'amount' => null,
            ])->toArray()
        )->assertStatus(400);

        $this->actingAs($this->admin)->post(
            URL::route('bazar.orders.transactions.store', $this->order),
            $payment = Transaction::factory()->make([
                'type' => 'payment',
                'driver' => 'cash',
                'amount' => $this->order->fresh()->totalPayable(),
            ])->toArray()
        )->assertCreated()
         ->assertJson($payment);

         $this->actingAs($this->admin)->post(
            URL::route('bazar.orders.transactions.store', $this->order),
            $refund = Transaction::factory()->make([
                'type' => 'refund',
                'driver' => 'cash',
                'amount' => $this->order->totalRefundable(),
            ])->toArray()
        )->assertCreated()
         ->assertJson($refund);

        $this->assertDatabaseHas('bazar_transactions', ['amount' => $payment['amount'], 'type' => $payment['type']]);
        $this->assertDatabaseHas('bazar_transactions', ['amount' => $refund['amount'], 'type' => $refund['type']]);
    }

    /** @test */
    public function an_admin_can_update_transaction()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.orders.transactions.update', [$this->order, $this->transaction]))
            ->assertForbidden();

        $this->assertFalse($this->transaction->completed());

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.orders.transactions.update', [$this->order, $this->transaction]))
            ->assertOk()
            ->assertExactJson(['updated' => true]);

        $this->assertTrue($this->transaction->fresh()->completed());

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.orders.transactions.update', [$this->order, $this->transaction]))
            ->assertOk()
            ->assertExactJson(['updated' => true]);

        $this->assertTrue($this->transaction->fresh()->pending());
    }

    /** @test */
    public function an_admin_can_destroy_transaction()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.orders.transactions.destroy', [$this->order, $this->transaction]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.orders.transactions.destroy', [$this->order, $this->transaction]))
            ->assertOk()
            ->assertExactJson(['deleted' => true]);

        $this->assertDatabaseMissing('bazar_transactions', ['id' => $this->transaction->id]);
    }
}

class FakeGateway extends Driver
{
    public function pay(Order $order, float $amount = null, array $attributes = []): Transaction
    {
        throw new TransactionFailedException('Payment failed');

        return new Transaction();
    }

    public function refund(Order $order, float $amount = null, array $attributes = []): Transaction
    {
        throw new TransactionFailedException('Refund failed');

        return new Transaction();
    }
}
