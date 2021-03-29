<?php

namespace Bazar\Listeners;

use Bazar\Events\OrderPlaced;
use Bazar\Models\User;
use Bazar\Notifications\AdminNewOrder;
use Bazar\Notifications\CustomerNewOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class SendNewOrderNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \Bazar\Events\OrderPlaced  $event
     * @return void
     */
    public function handle(OrderPlaced $event): void
    {
        $users = User::proxy()->newQuery()->whereIn('email', Config::get('bazar.admins', []))->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new AdminNewOrder($event->order));
        }

        if ($email = $event->order->address->email) {
            Notification::route('mail', $email)->notify(new CustomerNewOrder($event->order));
        }
    }
}
