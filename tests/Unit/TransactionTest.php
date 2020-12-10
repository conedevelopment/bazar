<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\TransactionFactory;
use Bazar\Support\Facades\Gateway;
use Bazar\Tests\TestCase;

class TransactionTest extends TestCase
{
    protected $order, $transaction;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = $this->admin->orders()->save(OrderFactory::new()->make());
        $this->transaction = TransactionFactory::new()->make();
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
        $this->assertSame(Gateway::driver($this->transaction->driver)->name(), $this->transaction->driverName);
        $this->assertSame('fake', $this->transaction->driver('fake')->driverName);
    }

    /** @test */
    public function it_has_url()
    {
        $this->assertSame(
            Gateway::driver($this->transaction->driver)->transactionUrl($this->transaction),
            $this->transaction->url
        );

        $this->assertNull($this->transaction->driver('fake')->url);
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
            $this->transaction->newQuery()->where('type', 'payment')->toSql(),
            $this->transaction->newQuery()->payment()->toSql()
        );

        $this->assertSame(
            $this->transaction->newQuery()->where('type', 'refund')->toSql(),
            $this->transaction->newQuery()->refund()->toSql()
        );

        $this->assertSame(
            $this->transaction->newQuery()->whereNotNull('completed_at')->toSql(),
            $this->transaction->newQuery()->completed()->toSql()
        );

        $this->assertSame(
            $this->transaction->newQuery()->whereNull('completed_at')->toSql(),
            $this->transaction->newQuery()->pending()->toSql()
        );
    }
}
