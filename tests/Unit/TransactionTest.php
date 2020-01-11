<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Order;
use Bazar\Models\Transaction;
use Bazar\Tests\TestCase;

class TransactionTest extends TestCase
{
    protected $order, $transaction;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = $this->admin->orders()->save(factory(Order::class)->make());
        $this->transaction = $this->order->transactions()->save(factory(Transaction::class)->make());
    }

    /** @test */
    public function a_transaction_belongs_to_an_order()
    {
        $this->assertTrue(
            $this->order->transactions->pluck('id')->contains($this->transaction->id)
        );
    }
}
