<?php

namespace Bazar\Mail;

use Bazar\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerNewOrder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \Bazar\Models\Order
     */
    public $order;

    /**
     * Create a new message instance.
     *
     * @param  \Bazar\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): CustomerNewOrder
    {
        return $this->markdown('bazar::emails.new-order');
    }
}
