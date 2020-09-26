<?php

namespace Bazar\Http\Controllers;

use Bazar\Exceptions\TransactionFailedException;
use Bazar\Http\Requests\TransactionStoreRequest as StoreRequest;
use Bazar\Http\Requests\TransactionUpdateRequest as UpdateRequest;
use Bazar\Models\Order;
use Bazar\Models\Transaction;
use Bazar\Support\Facades\Gateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Throwable;

class TransactionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor(Transaction::class)) {
            $this->authorizeResource(Transaction::class);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\TransactionStoreRequest  $request
     * @param  \Bazar\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, Order $order): JsonResponse
    {
        $method = $request->input('type') === 'refund' ? 'refund' : 'pay';

        try {
            $transaction = call_user_func_array(
                [Gateway::driver($request->input('driver')), $method],
                [$order, $request->amount ? (float) $request->amount : null]
            );
        } catch (Throwable $e) {
            return Response::json(
                ['message' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return Response::json($transaction, JsonResponse::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\TransactionUpdateRequest  $request
     * @param  \Bazar\Models\Order  $order
     * @param  \Bazar\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Order $order, Transaction $transaction): JsonResponse
    {
        if ($transaction->completed()) {
            $transaction->markAsPending();
        } else {
            $transaction->markAsCompleted();
        }

        return Response::json(['updated' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\Order
     * @param  \Bazar\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Order $order, Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return Response::json(['deleted' => true]);
    }
}
