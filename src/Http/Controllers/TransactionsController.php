<?php

namespace Bazar\Http\Controllers;

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
            return Response::json(['message' => $e->getMessage()], 400);
        }

        return Response::json($transaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\TransactionUpdateRequest  $request
     * @param  int  $order
     * @param  \Bazar\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, int $order, Transaction $transaction): JsonResponse
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
     * @param  int  $order
     * @param  \Bazar\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $order, Transaction $transaction)
    {
        $transaction->delete();

        return Response::json(['deleted' => true]);
    }
}
