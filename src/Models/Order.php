<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Concerns\Addressable;
use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Filterable;
use Bazar\Concerns\InteractsWithDiscounts;
use Bazar\Concerns\InteractsWithItems;
use Bazar\Concerns\InteractsWithProxy;
use Bazar\Contracts\Models\Order as Contract;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Exceptions\TransactionFailedException;
use Bazar\Support\Facades\Gateway;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Order extends Model implements Contract
{
    use Addressable;
    use BazarRoutable;
    use HasFactory;
    use InteractsWithDiscounts;
    use InteractsWithItems;
    use InteractsWithProxy;
    use SoftDeletes;
    use Filterable {
        Filterable::filters as defaultFilters;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'tax',
        'total',
        'net_total',
        'status_name',
        'formatted_tax',
        'formatted_total',
        'formatted_discount',
        'formatted_net_total',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'discount' => 0,
        'currency' => null,
        'status' => 'pending',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'discount' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'currency',
        'discount',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_orders';

    /**
     * Get the proxied contract.
     *
     * @return string
     */
    public static function getProxiedContract(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Bazar\Database\Factories\OrderFactory
     */
    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    /**
     * Get the available order statuses.
     *
     * @return array
     */
    public static function statuses(): array
    {
        return [
            __('Pending') => 'pending',
            __('On Hold') => 'on_hold',
            __('In Progress') => 'in_progress',
            __('Completed') => 'completed',
            __('Cancelled') => 'cancelled',
            __('Failed') => 'failed',
            __('Refunded') => 'refunded',
        ];
    }

    /**
     * Get the filter options for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function filters(Request $request): array
    {
        return array_merge(static::defaultFilters($request), [
            'status' => static::statuses(),
            'user' => User::proxy()->newQuery()->pluck('id', 'name')->toArray(),
        ]);
    }

    /**
     * Get the cart for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::getProxiedClass());
    }

    /**
     * Get the transactions for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::getProxiedClass());
    }

    /**
     * Get the payments attribute.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPaymentsAttribute(): Collection
    {
        return $this->transactions->where('type', Transaction::PAYMENT);
    }

    /**
     * Get the refunds attribute.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRefundsAttribute(): Collection
    {
        return $this->transactions->where('type', Transaction::REFUND);
    }

    /**
     * Get the status name attribute.
     *
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return array_search($this->status, static::statuses()) ?: $this->status;
    }

    /**
     * Create a payment transaction for the order.
     *
     * @param  float|null  $amount
     * @param  string|null  $driver
     * @param  array  $attributes
     * @return \Bazar\Models\Transaction
     *
     * @throws \Bazar\Exceptions\TransactionFailedException
     */
    public function pay(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction
    {
        if ($this->totalPayable() === 0.0 || $this->paid()) {
            throw new TransactionFailedException("Order #{$this->id} is fully paid.");
        }

        $transaction = $this->transactions()->create(array_replace($attributes, [
            'type' => Transaction::PAYMENT,
            'driver' => $driver ?: Gateway::getDefaultDriver(),
            'amount' => is_null($amount) ? $this->totalPayable() : min($amount, $this->totalPayable()),
        ]));

        $this->transactions->push($transaction);

        return $transaction;
    }

    /**
     * Create a refund transaction for the order.
     *
     * @param  float|null  $amount
     * @param  string|null  $driver
     * @param  array  $attributes
     * @return \Bazar\Models\Transaction
     *
     * @throws \Bazar\Exceptions\TransactionFailedException
     */
    public function refund(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction
    {
        if ($this->totalRefundable() === 0.0 || $this->refunded()) {
            throw new TransactionFailedException("Order #{$this->id} is fully refunded.");
        }

        $transaction = $this->transactions()->create(array_replace($attributes, [
            'type' => Transaction::REFUND,
            'driver' => $driver ?: Gateway::getDefaultDriver(),
            'amount' => is_null($amount) ? $this->totalRefundable() : min($amount, $this->totalRefundable()),
        ]));

        $this->transactions->push($transaction);

        return $transaction;
    }

    /**
     * Get the total paid amount.
     *
     * @return float
     */
    public function totalPaid(): float
    {
        return $this->payments->sum('amount');
    }

    /**
     * Get the total refunded amount.
     *
     * @return float
     */
    public function totalRefunded(): float
    {
        return $this->refunds->sum('amount');
    }

    /**
     * Get the total payable amount.
     *
     * @return float
     */
    public function totalPayable(): float
    {
        return $this->total() - $this->totalPaid();
    }

    /**
     * Get the total refundabke amount.
     *
     * @return float
     */
    public function totalRefundable(): float
    {
        return $this->totalPaid() - $this->totalRefunded();
    }

    /**
     * Determine if the order is fully paid.
     *
     * @return bool
     */
    public function paid(): bool
    {
        return $this->payments->isNotEmpty() && $this->total() <= $this->totalPaid();
    }

    /**
     * Determine if the order is fully refunded.
     *
     * @return bool
     */
    public function refunded(): bool
    {
        return $this->refunds->isNotEmpty() && $this->totalPaid() <= $this->totalRefunded();
    }

    /**
     * Set the status by the given value.
     *
     * @param  string  $status
     * @return void
     */
    public function markAs(string $status): void
    {
        if ($this->status !== $status) {
            $this->setAttribute('status', $status)->save();
        }
    }

    /**
     * Scope the query only to the given search term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->whereHas('address', static function (Builder $query) use ($value): Builder {
            return $query->where($query->getModel()->qualifyColumn('first_name'), 'like', "{$value}%")
                         ->orWhere($query->getModel()->qualifyColumn('last_name'), 'like', "{$value}%");
        });
    }

    /**
     * Scope a query to only include orders with the given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where($query->qualifyColumn('status'), $status);
    }

    /**
     * Scope the query to the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUser(Builder $query, int $value): Builder
    {
        return $query->whereHas('user', static function (Builder $query) use ($value): Builder {
            return $query->where($query->getModel()->qualifyColumn('id'), $value);
        });
    }

    /**
     * Get the breadcrumb representation of the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function toBreadcrumb(Request $request): string
    {
        return sprintf('#%d', $this->id);
    }
}
