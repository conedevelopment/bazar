<?php

namespace Cone\Bazar\Http\Controllers;

use Cone\Bazar\Gateway\Response;
use Cone\Bazar\Support\Facades\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    /**
     * Handle the capture request.
     */
    public function capture(Request $request, string $driver): Response
    {
        $gateway = Gateway::driver($driver);

        return $gateway->handleCapture(
            $request, $gateway->resolveOrderForCapture($request)
        );
    }

    /**
     * Handle the notification request.
     */
    public function notification(Request $request, string $driver): Response
    {
        return Gateway::driver($driver)->handleNotification($request);
    }
}
