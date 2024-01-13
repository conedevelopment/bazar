<?php

namespace Cone\Bazar\Http\Controllers;

use Cone\Bazar\Support\Facades\Gateway;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GatewayController extends Controller
{
    /**
     * Handle the capture request.
     */
    public function capture(Request $request, string $driver): Response
    {
        return Gateway::driver($driver)->handleCapture($request)->toResponse($request);
    }

    /**
     * Handle the notification requrest.
     */
    public function notification(Request $request, string $driver): Response
    {
        return Gateway::driver($driver)->handleNotification($request)->toResponse($request);
    }
}

