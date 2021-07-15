<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Bazar\Tests\TestCase;

class TransactionTest extends TestCase
{
    protected $order, $transaction;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = $this->admin->orders()->save(Order::factory()->make());
        $this->transaction = Transaction::factory()->make();
        $this->transaction->order()->associate($this->order)->save();
    }

    /** @test */
    public function it_belongs_to_an_order()
    {
        $this->assertSame($this->order->id, $this->transaction->order_id);
    }

    /** @test */
    public function it_has_driver_name()
    {
        $this->assertSame(
            Gateway::driver($this->transaction->driver)->getName(),
            $this->transaction->driverName
        );
    }

    /** @test */
    public function it_has_url()
    {
        $this->assertSame(
            Gateway::driver($this->transaction->driver)->getTransactionUrl($this->transaction),
            $this->transaction->url
        );

        $this->transaction->setAttribute('driver', 'fake');
        $this->assertNull($this->transaction->url);
    }

    /** @test */
    public function it_can_be_completed()
    {
        $this->transaction->markAsPending();
        $this->assertNull($this->transaction->completed_at);
        $this->assertTrue($this->transaction->pending());
        $this->assertFalse($this->transaction->completed());

        $this->transaction->markAsCompleted();
        $this->assertNotNull($this->transaction->completed_at);
        $this->assertTrue($this->transaction->completed());
        $this->assertFalse($this->transaction->pending());
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->transaction->newQuery()->where('bazar_transactions.type', 'payment')->toSql(),
            $this->transaction->newQuery()->payment()->toSql()
        );

        $this->assertSame(
            $this->transaction->newQuery()->where('bazar_transactions.type', 'refund')->toSql(),
            $this->transaction->newQuery()->refund()->toSql()
        );

        $this->assertSame(
            $this->transaction->newQuery()->whereNotNull('bazar_transactions.completed_at')->toSql(),
            $this->transaction->newQuery()->completed()->toSql()
        );

        $this->assertSame(
            $this->transaction->newQuery()->whereNull('bazar_transactions.completed_at')->toSql(),
            $this->transaction->newQuery()->pending()->toSql()
        );
    }
}
