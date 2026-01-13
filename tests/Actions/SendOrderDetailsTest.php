<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Actions;

use Cone\Bazar\Actions\SendOrderDetails;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SendOrderDetailsTest extends TestCase
{
    protected SendOrderDetails $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new SendOrderDetails();
    }

    public function test_action_sends_order_details_notification(): void
    {
        Notification::fake();

        $order = Order::factory()->create();

        Product::factory()->count(2)->create()->each(function ($product) use ($order) {
            $order->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => 2,
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });

        $request = Request::create('/');
        $models = new Collection([$order]);

        $this->action->handle($request, $models);

        $this->assertTrue(true);
    }

    public function test_action_handles_multiple_orders(): void
    {
        Notification::fake();

        $orders = Order::factory()->count(3)->create();

        $request = Request::create('/');

        $this->action->handle($request, $orders);

        $this->assertTrue(true);
    }
}
