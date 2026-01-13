<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Http\Controllers;

use Cone\Bazar\Gateway\CashDriver;
use Cone\Bazar\Gateway\Response;
use Cone\Bazar\Http\Controllers\GatewayController;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Http\Request;

class GatewayControllerTest extends TestCase
{
    protected GatewayController $controller;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new GatewayController();

        $this->order = Order::factory()->create();

        Product::factory()->count(2)->create()->each(function ($product) {
            $this->order->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => 2,
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });
    }

    public function test_controller_handles_capture_request(): void
    {
        $request = Request::create('/gateway/cash/capture', 'POST', [
            'order_id' => $this->order->id,
        ]);

        $this->mock(CashDriver::class, function ($mock) {
            $mock->shouldReceive('resolveOrderForCapture')
                ->once()
                ->andReturn($this->order);

            $mock->shouldReceive('handleCapture')
                ->once()
                ->andReturn(new Response(['status' => 'success']));
        });

        Gateway::shouldReceive('driver')
            ->with('cash')
            ->andReturn($this->app->make(CashDriver::class));

        $response = $this->controller->capture($request, 'cash');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_controller_handles_invalid_capture_request(): void
    {
        $request = Request::create('/gateway/invalid/capture', 'POST');

        Gateway::shouldReceive('driver')
            ->with('invalid')
            ->andThrow(new \Exception('Invalid driver'));

        $response = $this->controller->capture($request, 'invalid');

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString('Invalid request', $response->getContent());
    }

    public function test_controller_handles_notification_request(): void
    {
        $request = Request::create('/gateway/cash/notification', 'POST', [
            'order_id' => $this->order->id,
        ]);

        $this->mock(CashDriver::class, function ($mock) {
            $mock->shouldReceive('resolveOrderForNotification')
                ->once()
                ->andReturn($this->order);

            $mock->shouldReceive('handleNotification')
                ->once()
                ->andReturn(new Response(['status' => 'success']));
        });

        Gateway::shouldReceive('driver')
            ->with('cash')
            ->andReturn($this->app->make(CashDriver::class));

        $response = $this->controller->notification($request, 'cash');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_controller_handles_invalid_notification_request(): void
    {
        $request = Request::create('/gateway/invalid/notification', 'POST');

        Gateway::shouldReceive('driver')
            ->with('invalid')
            ->andThrow(new \Exception('Invalid driver'));

        $response = $this->controller->notification($request, 'invalid');

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString('Invalid request', $response->getContent());
    }
}
