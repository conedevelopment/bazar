<?php

namespace Cone\Bazar\Jobs;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Notifications\CustomerNewOrder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendNewOrderNotifications
{
    use Dispatchable;
    use SerializesModels;

    /**
     * The order instance.
     *
     * @var \Cone\Bazar\Models\Order
     */
    public Order $order;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param  \Cone\Bazar\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($email = $this->order->address->email) {
            Notification::route('mail', $email)->notify(new CustomerNewOrder($this->order));
        }
    }
}
