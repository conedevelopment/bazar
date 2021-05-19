<?php

namespace Bazar\Jobs;

use Bazar\Models\Order;
use Bazar\Models\User;
use Bazar\Notifications\AdminNewOrder;
use Bazar\Notifications\CustomerNewOrder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class SendNewOrderNotifications
{
    use Dispatchable;
    use SerializesModels;

    /**
     * The order instance.
     *
     * @var \Bazar\Models\Order
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
     * @param  \Bazar\Models\Order  $order
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
        $users = User::proxy()->newQuery()->whereIn('email', Config::get('bazar.admins', []))->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new AdminNewOrder($this->order));
        }

        if ($email = $this->order->address->email) {
            Notification::route('mail', $email)->notify(new CustomerNewOrder($this->order));
        }
    }
}
