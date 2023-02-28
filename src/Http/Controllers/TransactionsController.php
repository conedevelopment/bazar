<?php

namespace Cone\Bazar\Http\Controllers;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Root\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class TransactionsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function store(Request $request, Order $order): JsonResponse
    {
        $data = $request->validated();

        try {
            $transaction = $data['type'] === Transaction::REFUND
                ? Gateway::driver($data['driver'])->refund($order, $data['amount'])
                : $order->pay($data['amount'], $data['driver'], array_merge(
                    ['completed_at' => time()],
                    Arr::except($data, ['type', 'amount', 'driver'])
                ));
        } catch (Throwable $exception) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, $exception->getMessage());
        }

        return new JsonResponse($transaction, JsonResponse::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order, Transaction $transaction): JsonResponse
    {
        if ($transaction->completed()) {
            $transaction->markAsPending();
        } else {
            $transaction->markAsCompleted();
        }

        return new JsonResponse($transaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order, Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }
}
