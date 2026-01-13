<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Notifications;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Notifications\OrderDetails;
use Cone\Bazar\Tests\TestCase;
use Cone\Bazar\Tests\User;
use Illuminate\Support\Facades\Notification;

class OrderDetailsTest extends TestCase
{
    protected Order $order;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->order = Order::factory()->create(['user_id' => $this->user->id]);

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

    public function test_notification_uses_mail_channel(): void
    {
        $notification = new OrderDetails($this->order);

        $channels = $notification->via($this->user);

        $this->assertContains('mail', $channels);
    }

    public function test_notification_creates_mail_message(): void
    {
        $notification = new OrderDetails($this->order);

        $mailMessage = $notification->toMail($this->user);

        $this->assertNotNull($mailMessage);
        $this->assertStringContainsString('Order Details', $mailMessage->subject);
    }

    public function test_notification_can_be_queued(): void
    {
        Notification::fake();

        $this->user->notify(new OrderDetails($this->order));

        Notification::assertSentTo($this->user, OrderDetails::class);
    }

    public function test_notification_includes_order_in_mail(): void
    {
        $notification = new OrderDetails($this->order);

        $mailMessage = $notification->toMail($this->user);

        $this->assertNotNull($mailMessage->viewData);
        $this->assertArrayHasKey('order', $mailMessage->viewData);
        $this->assertSame($this->order, $mailMessage->viewData['order']);
    }
}
