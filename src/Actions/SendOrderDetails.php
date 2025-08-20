<?php

declare(strict_types=1);

namespace Cone\Bazar\Actions;

use Cone\Bazar\Models\Order;
use Cone\Root\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SendOrderDetails extends Action
{
    /**
     * Handle the action.
     */
    public function handle(Request $request, Collection $models): void
    {
        $models->each(static function (Order $order): void {
            $order->sendOrderDetailsNotification();
        });
    }
}
