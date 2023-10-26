<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\OrderFactory;
use Cone\Bazar\Exceptions\TransactionFailedException;
use Cone\Bazar\Interfaces\Models\Order as Contract;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Bazar\Traits\Addressable;
use Cone\Bazar\Traits\InteractsWithDiscounts;
use Cone\Bazar\Traits\InteractsWithItems;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Order extends Model implements Contract
{
    use Addressable;
    use HasFactory;
    use InteractsWithDiscounts;
    use InteractsWithItems;
    use InteractsWithProxy;
    use SoftDeletes;

    public const PENDING = 'pending';
    public const ON_HOLD = 'on_hold';
    public const IN_PROGRESS = 'in_progress';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    public const FAILED = 'failed';
    public const REFUNDED = 'refunded';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
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
     * @var array<string, mixed>
     */
    protected $attributes = [
        'currency' => null,
        'discount' => 0,
        'status' => self::PENDING,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
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
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return OrderFactory::new();
    }

    /**
     * Get the available order statuses.
     */
    public static function statuses(): array
    {
        return [
            static::PENDING => __('Pending'),
            static::ON_HOLD => __('On Hold'),
            static::IN_PROGRESS => __('In Progress'),
            static::COMPLETED => __('Completed'),
            static::CANCELLED => __('Cancelled'),
            static::FAILED => __('Failed'),
            static::REFUNDED => __('Refunded'),
        ];
    }

    /**
     * Get the cart for the order.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::getProxiedClass());
    }

    /**
     * Get the transactions for the order.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::getProxiedClass());
    }

    /**
     * Get the payments attribute.
     */
    protected function payments(): Attribute
    {
        return new Attribute(
            get: function (): Collection {
                return $this->transactions->where('type', Transaction::PAYMENT);
            }
        );
    }

    /**
     * Get the refunds attribute.
     */
    protected function refunds(): Attribute
    {
        return new Attribute(
            get: function (): Collection {
                return $this->transactions->where('type', Transaction::REFUND);
            }
        );
    }

    /**
     * Get the status name attribute.
     */
    protected function statusName(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): string {
                return static::statuses()[$attributes['status']] ?? $attributes['status'];
            }
        );
    }

    /**
     * Create a payment transaction for the order.
     */
    public function pay(float $amount = null, string $driver = null, array $attributes = []): Transaction
    {
        if ($this->getTotalPayable() === 0.0 || $this->paid()) {
            throw new TransactionFailedException("Order #{$this->getKey()} is fully paid.");
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
     */
    public function refund(float $amount = null, string $driver = null, array $attributes = []): Transaction
    {
        if ($this->getTotalRefundable() === 0.0 || $this->refunded()) {
            throw new TransactionFailedException("Order #{$this->getKey()} is fully refunded.");
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
     */
    public function getTotalPaid(): float
    {
        return $this->payments->sum('amount');
    }

    /**
     * Get the total refunded amount.
     */
    public function getTotalRefunded(): float
    {
        return $this->refunds->sum('amount');
    }

    /**
     * Get the total payable amount.
     */
    public function getTotalPayable(): float
    {
        return $this->getTotal() - $this->getTotalPaid();
    }

    /**
     * Get the total refundabke amount.
     */
    public function getTotalRefundable(): float
    {
        return $this->getTotalPaid() - $this->getTotalRefunded();
    }

    /**
     * Determine if the order is fully paid.
     */
    public function paid(): bool
    {
        return $this->payments->isNotEmpty() && $this->getTotal() <= $this->getTotalPaid();
    }

    /**
     * Determine if the order is fully refunded.
     */
    public function refunded(): bool
    {
        return $this->refunds->isNotEmpty() && $this->getTotalPaid() <= $this->getTotalRefunded();
    }

    /**
     * Determine if the orderis partially refunded.
     */
    public function isPartiallyRefunded(): bool
    {
        return $this->refunds->isNotEmpty() && $this->getTotalPaid() > $this->getTotalRefunded();
    }

    /**
     * Set the status by the given value.
     */
    public function markAs(string $status): void
    {
        if ($this->status !== $status) {
            $this->setAttribute('status', $status)->save();
        }
    }

    /**
     * Scope a query to only include orders with the given status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where($query->qualifyColumn('status'), $status);
    }

    /**
     * Scope the query to the given user.
     */
    public function scopeUser(Builder $query, int $value): Builder
    {
        return $query->whereHas('user', static function (Builder $query) use ($value): Builder {
            return $query->whereKey($value);
        });
    }
}
