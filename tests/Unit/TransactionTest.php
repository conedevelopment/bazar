<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\TransactionFactory;
use Bazar\Tests\TestCase;

class TransactionTest extends TestCase
{
    protected $order, $transaction;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = $this->admin->orders()->save(OrderFactory::new()->make());
        $this->transaction = $this->order->transactions()->save(TransactionFactory::new()->make());
    }

    /** @test */
    public function a_transaction_belongs_to_an_order()
    {
        $this->assertTrue(
            $this->order->transactions->pluck('id')->contains($this->transaction->id)
        );
    }
}
