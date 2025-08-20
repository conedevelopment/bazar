<?php

declare(strict_types=1);

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Gateway\Driver;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Rules\TransactionAmount;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Select;
use Cone\Root\Fields\Text;
use Cone\Root\Fields\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Transactions extends HasMany
{
    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'order',
    ];

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Transactions'), $modelAttribute ?: 'transactions', $relation);

        $this->asSubResource();
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            Select::make(__('Type'), 'type')
                ->options([
                    Transaction::PAYMENT => __('Payment'),
                    Transaction::REFUND => __('Refund'),
                ]),

            Number::make(__('Amount'), 'amount')
                ->min(0)
                ->required()
                ->format(static function (Request $request, Transaction $transaction, ?float $value): string {
                    return (new Currency($value ?: 0, $transaction->order->currency))->format();
                })
                ->rules(static function (Request $request, Transaction $transaction): array {
                    return [
                        'required',
                        'numeric',
                        'gt:0',
                        new TransactionAmount($transaction->fill(['type' => $request->input('type')])),
                    ];
                }),

            Select::make(__('Driver'), 'driver')
                ->options(static function (Request $request, Transaction $transaction): array {
                    return array_map(static function (Driver $driver): string {
                        return $driver->getName();
                    }, Gateway::getAvailableDrivers($transaction->order));
                }),

            Text::make(__('Key'), 'key')
                ->searchable()
                ->sortable(),

            URL::make(__('URL'), static function (Request $request, Transaction $transaction): ?string {
                return $transaction->url;
            }),

            Date::make(__('Completed At'), 'completed_at')
                ->withTime(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function saved(Request $request, Model $model): void
    {
        if ($model->wasRecentlyCreated) {
            Gateway::driver($model->driver)->handleManualTransaction($model);
        }
    }
}
