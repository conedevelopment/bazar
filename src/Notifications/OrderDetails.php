<?php

declare(strict_types=1);

namespace Cone\Bazar\Notifications;

use Cone\Bazar\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDetails extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The order instance.
     */
    protected Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Order Details (#:order)', ['order' => $this->order->getKey()]))
            ->markdown('root::mail.order-details', [
                'order' => $this->order,
            ]);
    }
}
