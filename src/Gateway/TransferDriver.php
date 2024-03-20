<?php

namespace Cone\Bazar\Gateway;

use Exception;
use Illuminate\Http\Request;

class TransferDriver extends Driver
{
    /**
     * The driver name.
     */
    protected string $name = 'transfer';

    /**
     * {@inheritdoc}
     */
    public function handleNotification(Request $request): Response
    {
        throw new Exception('This payment gateway does not support payment notifications.');
    }
}
