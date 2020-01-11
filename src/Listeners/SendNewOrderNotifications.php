<?php

namespace Bazar\Listeners;

use Bazar\Events\OrderPlaced;
use Bazar\Mail\NewOrderMail;
use Bazar\Models\User;
use Bazar\Notifications\NewOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
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
        if ($users = User::whereIn('email', config('bazar.admins', []))->get()) {
            Notification::send($users, new NewOrderNotification($event->order));
        }

        if ($email = $event->order->address->email) {
            Mail::to($email)->send(new NewOrderMail($event->order));
        }
    }
}
