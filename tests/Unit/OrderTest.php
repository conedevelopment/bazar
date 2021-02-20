<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\TransactionFactory;
use Bazar\Tests\TestCase;

class OrderTest extends TestCase
{
    protected $order, $products;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = OrderFactory::new()->create();

        $this->products = ProductFactory::new()->count(3)->create()->mapWithKeys(function ($product) {
            return [$product->id => ['quantity' => mt_rand(1, 5), 'tax' => 0, 'price' => $product->price]];
        });

        $this->order->products()->attach($this->products->all());
    }

    /** @test */
    public function it_can_belong_to_a_customer()
    {
        $this->assertNull($this->order->user);

        $this->order->user()->associate($this->user);

        $this->order->save();

        $this->assertSame($this->user->id, $this->order->user_id);
    }

    /** @test */
    public function it_has_transactions()
    {
        $transactions = $this->order->transactions()->saveMany(
            TransactionFactory::new()->count(3)->make()
        );

        $this->assertSame(
            $this->order->transactions->pluck('id')->all(), $transactions->pluck('id')->all()
        );
    }

    /** @test */
    public function it_has_address()
    {
        $address = $this->order->address()->save(
            AddressFactory::new()->make()
        );

        $this->assertSame($address->id, $this->order->address->id);
    }

    /** @test */
    public function it_has_products()
    {
        $product = ProductFactory::new()->create();

        $this->order->products()->attach($product, ['price' => 100, 'tax' => 0, 'quantity' => 3]);

        $this->assertTrue(
            $this->order->products->pluck('id')->contains($product->id)
        );
    }

    /** @test */
    public function it_has_total_attribute()
    {
        $total = $this->products->sum(function ($product) {
            return ($product['price'] + $product['tax']) * $product['quantity'];
        });

        $total -= $this->order->discount;

        $this->assertEquals($total, $this->order->total);
    }

    /** @test */
    public function it_has_net_total_attribute()
    {
        $total = $this->products->sum(function ($product) {
            return $product['price'] * $product['quantity'];
        });

        $total -= $this->order->discount;

        $this->assertEquals($total, $this->order->netTotal);
    }

    /** @test */
    public function it_is_breadcrumbable()
    {
        $this->assertInstanceOf(Breadcrumbable::class, $this->order);
        $this->assertSame("#{$this->order->id}", $this->order->toBreadcrumb($this->app['request']));
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->order->newQuery()->whereHas('address', function ($q) {
                $q->where('bazar_addresses.first_name', 'like', 'test%')
                    ->orWhere('bazar_addresses.last_name', 'like', 'test%');
            })->toSql(),
            $this->order->newQuery()->search('test')->toSql()
        );

        $this->assertSame(
            $this->order->newQuery()->whereIn('status', ['pending'])->toSql(),
            $this->order->newQuery()->status('pending')->toSql()
        );

        $this->assertSame(
            $this->order->newQuery()->whereHas('user', function ($q) {
                $q->where('users.id', 1);
            })->toSql(),
            $this->order->newQuery()->user(1)->toSql()
        );
    }
}
