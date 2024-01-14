<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Gateway\Response;
use Exception;
use Illuminate\Http\Request;

class TransferDriver extends Driver
{
    /**
     * The driver name.
     */
    protected string $name = 'transfer';

    /**
     * Handle the notification request.
     */
    public function handleNotification(Request $request): Response
    {
        throw new Exception('This payment gateway does not support payment notifications.');
    }
}
