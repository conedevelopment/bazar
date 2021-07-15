<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Notifications\AdminNewOrder;
use Cone\Bazar\Notifications\CustomerNewOrder;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class NotificationTest extends TestCase
{
    /** @test */
    public function admin_order_notification_can_be_sent_on_different_channels()
    {
        $order = Order::factory()->create();
        $notification = new AdminNewOrder($order);

        $this->assertInstanceOf(MailMessage::class, $notification->toMail($this->admin));
        $this->assertSame([
            'message' => __('A new order has been placed.'),
            'url' => URL::route('bazar.orders.show', $order),
        ], $notification->toArray($this->admin));
    }

    /** @test */
    public function customer_order_notification_can_be_sent_on_different_channels()
    {
        $order = Order::factory()->create();
        $notification = new CustomerNewOrder($order);

        $this->assertInstanceOf(MailMessage::class, $notification->toMail($this->user));
        $this->assertSame([], $notification->toArray($this->user));
    }
}
