<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\OrderFactory;
use Cone\Bazar\Exceptions\TransactionFailedException;
use Cone\Bazar\Interfaces\Models\Order as Contract;
use Cone\Bazar\Resources\OrderResource;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Bazar\Traits\Addressable;
use Cone\Bazar\Traits\InteractsWithDiscounts;
use Cone\Bazar\Traits\InteractsWithItems;
use Cone\Root\Interfaces\Resourceable;
use Cone\Root\Resources\Resource;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Order extends Model implements Contract, Resourceable
{
    use Addressable;
    use HasFactory;
    use InteractsWithDiscounts;
    use InteractsWithItems;
    use InteractsWithProxy;
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'formatted_discount',
        'formatted_net_total',
        'formatted_tax',
        'formatted_total',
        'net_total',
        'status_name',
        'tax',
        'total',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'currency' => null,
        'discount' => 0,
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
        'currency',
        'discount',
        'status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_orders';

    /**
     * Get the proxied interface.
     *
     * @return string
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
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
            'pending' => __('Pending'),
            'on_hold' => __('On Hold'),
            'in_progress' => __('In Progress'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
            'failed' => __('Failed'),
            'refunded' => __('Refunded'),
        ];
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
        return static::statuses()[$this->status] ?? $this->status;
    }

    /**
     * Create a payment transaction for the order.
     *
     * @param  float|null  $amount
     * @param  string|null  $driver
     * @param  array  $attributes
     * @return \Cone\Bazar\Models\Transaction
     *
     * @throws \Cone\Bazar\Exceptions\TransactionFailedException
     */
    public function pay(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction
    {
        if ($this->getTotalPayable() === 0.0 || $this->paid()) {
            throw new TransactionFailedException("Order #{$this->id} is fully paid.");
        }

        $transaction = $this->transactions()->create(array_replace($attributes, [
            'type' => Transaction::PAYMENT,
            'driver' => $driver ?: Gateway::getDefaultDriver(),
            'amount' => is_null($amount) ? $this->getTotalPayable() : min($amount, $this->getTotalPayable()),
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
     * @return \Cone\Bazar\Models\Transaction
     *
     * @throws \Cone\Bazar\Exceptions\TransactionFailedException
     */
    public function refund(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction
    {
        if ($this->getTotalRefundable() === 0.0 || $this->refunded()) {
            throw new TransactionFailedException("Order #{$this->id} is fully refunded.");
        }

        $transaction = $this->transactions()->create(array_replace($attributes, [
            'type' => Transaction::REFUND,
            'driver' => $driver ?: Gateway::getDefaultDriver(),
            'amount' => is_null($amount) ? $this->getTotalRefundable() : min($amount, $this->getTotalRefundable()),
        ]));

        $this->transactions->push($transaction);

        return $transaction;
    }

    /**
     * Get the total paid amount.
     *
     * @return float
     */
    public function getTotalPaid(): float
    {
        return $this->payments->sum('amount');
    }

    /**
     * Get the total refunded amount.
     *
     * @return float
     */
    public function getTotalRefunded(): float
    {
        return $this->refunds->sum('amount');
    }

    /**
     * Get the total payable amount.
     *
     * @return float
     */
    public function getTotalPayable(): float
    {
        return $this->getTotal() - $this->getTotalPaid();
    }

    /**
     * Get the total refundabke amount.
     *
     * @return float
     */
    public function getTotalRefundable(): float
    {
        return $this->getTotalPaid() - $this->getTotalRefunded();
    }

    /**
     * Determine if the order is fully paid.
     *
     * @return bool
     */
    public function paid(): bool
    {
        return $this->payments->isNotEmpty() && $this->getTotal() <= $this->getTotalPaid();
    }

    /**
     * Determine if the order is fully refunded.
     *
     * @return bool
     */
    public function refunded(): bool
    {
        return $this->refunds->isNotEmpty() && $this->getTotalPaid() <= $this->getTotalRefunded();
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
            return $query->where($query->qualifyColumn('id'), $value);
        });
    }

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource
    {
        return new OrderResource(static::class);
    }
}
