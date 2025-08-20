<?php

declare(strict_types=1);

namespace Cone\Bazar\Http\Controllers;

use Cone\Bazar\Support\Facades\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GatewayController extends Controller
{
    /**
     * Handle the capture request.
     */
    public function capture(Request $request, string $driver): Response
    {
        try {
            $gateway = Gateway::driver($driver);

            return $gateway->handleCapture(
                $request, $gateway->resolveOrderForCapture($request)
            )->toResponse($request);
        } catch (Throwable $exception) {
            report($exception);

            return ResponseFactory::make('Invalid request.', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Handle the notification request.
     */
    public function notification(Request $request, string $driver): Response
    {
        try {
            $gateway = Gateway::driver($driver);

            return $gateway->handleNotification(
                $request, $gateway->resolveOrderForNotification($request)
            )->toResponse($request);
        } catch (Throwable $exception) {
            report($exception);

            return ResponseFactory::make('Invalid request.', Response::HTTP_BAD_REQUEST);
        }
    }
}
