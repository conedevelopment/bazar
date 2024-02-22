<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\OrderFactory;
use Cone\Bazar\Events\OrderStatusChanged;
use Cone\Bazar\Exceptions\TransactionFailedException;
use Cone\Bazar\Interfaces\Models\Order as Contract;
use Cone\Bazar\Notifications\OrderDetails;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Bazar\Traits\Addressable;
use Cone\Bazar\Traits\InteractsWithDiscounts;
use Cone\Bazar\Traits\InteractsWithItems;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notification as Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as Notifier;
use Illuminate\Support\Number;

class Order extends Model implements Contract
{
    use Addressable;
    use HasFactory;
    use HasUuids;
    use InteractsWithDiscounts;
    use InteractsWithItems;
    use InteractsWithProxy;
    use SoftDeletes;

    public const CANCELLED = 'cancelled';

    public const FULFILLED = 'fulfilled';

    public const FAILED = 'failed';

    public const IN_PROGRESS = 'in_progress';

    public const ON_HOLD = 'on_hold';

    public const PENDING = 'pending';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'formatted_discount',
        'formatted_subtotal',
        'formatted_tax',
        'formatted_total',
        'subtotal',
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
        'status' => self::ON_HOLD,
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
    public static function getStatuses(): array
    {
        return [
            static::PENDING => __('Pending'),
            static::ON_HOLD => __('On Hold'),
            static::IN_PROGRESS => __('In Progress'),
            static::FULFILLED => __('Fulfilled'),
            static::CANCELLED => __('Cancelled'),
            static::FAILED => __('Failed'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
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
     * Get the base transaction for the order.
     */
    public function transaction(): HasOne
    {
        return $this->transactions()->one()->ofMany(
            ['id' => 'MIN'],
            static function (Builder $query): Builder {
                return $query->payment();
            }
        );
    }

    /**
     * Get the payments attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<\Illuminate\Support\Collection, never>
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
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<\Illuminate\Support\Collection, never>
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
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function statusName(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): string {
                return static::getStatuses()[$attributes['status']] ?? $attributes['status'];
            }
        );
    }

    /**
     * Get the payment status name attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function paymentStatus(): Attribute
    {
        return new Attribute(
            get: function (): string {
                return match (true) {
                    $this->refunded() => __('Refunded'),
                    $this->partiallyRefunded() => __('Partially Refunded'),
                    $this->paid() => __('Paid'),
                    $this->partiallyPaid() => __('Partially Paid'),
                    default => __('Pending Payment'),
                };
            }
        );
    }

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the discount rate.
     */
    public function getDiscountRate(): float
    {
        return $this->getSubtotal() > 0 ? ($this->getDiscount() / $this->getSubtotal()) * 100 : 0;
    }

    /**
     * Get the formatted discount rate.
     */
    public function getFormattedDiscountRate(): string
    {
        return Number::percentage($this->getDiscountRate());
    }

    /**
     * Create a payment transaction for the order.
     */
    public function pay(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction
    {
        if (! $this->payable()) {
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
    public function refund(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction
    {
        if (! $this->refundable()) {
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
        return $this->payments->filter->completed()->sum('amount');
    }

    /**
     * Get the total refunded amount.
     */
    public function getTotalRefunded(): float
    {
        return $this->refunds->filter->completed()->sum('amount');
    }

    /**
     * Get the total payable amount.
     */
    public function getTotalPayable(): float
    {
        return max($this->getTotal() - $this->getTotalPaid(), 0);
    }

    /**
     * Get the total refundabke amount.
     */
    public function getTotalRefundable(): float
    {
        return max($this->getTotalPaid() - $this->getTotalRefunded(), 0);
    }

    /**
     * Determine if the order is fully paid.
     */
    public function paid(): bool
    {
        return $this->payments->filter->completed()->isNotEmpty()
            && $this->getTotal() <= $this->getTotalPaid();
    }

    /**
     * Determine if the order is partially paid.
     */
    public function partiallyPaid(): bool
    {
        return $this->payments->filter->completed()->isNotEmpty()
            && $this->getTotal() > $this->getTotalPaid();
    }

    /**
     * Determine if the order is payable.
     */
    public function payable(): bool
    {
        return $this->getTotalPayable() > 0 && ! $this->paid();
    }

    /**
     * Determine if the order is fully refunded.
     */
    public function refunded(): bool
    {
        return $this->refunds->filter->completed()->isNotEmpty()
            && $this->getTotalPaid() <= $this->getTotalRefunded();
    }

    /**
     * Determine if the order is refundable.
     */
    public function refundable(): bool
    {
        return $this->getTotalRefundable() > 0 && ! $this->refunded();
    }

    /**
     * Determine if the orderis partially refunded.
     */
    public function partiallyRefunded(): bool
    {
        return $this->refunds->filter->completed()->isNotEmpty()
            && $this->getTotalPaid() > $this->getTotalRefunded();
    }

    /**
     * Set the status by the given value.
     */
    public function markAs(string $status): void
    {
        if ($this->status !== $status) {
            $from = $this->status;

            $this->setAttribute('status', $status)->save();

            OrderStatusChanged::dispatch($this, $status, $from);
        }
    }

    /**
     * Get the notifiable object.
     */
    public function getNotifiable(): object
    {
        return is_null($this->user)
            ? Notifier::route('mail', [$this->address->email => $this->address->name])
            : $this->user;
    }

    /**
     * Send the given notification.
     */
    public function sendNotification(Notification $notification): void
    {
        $this->getNotifiable()->notify($notification);
    }

    /**
     * Send the order details notification.
     */
    public function sendOrderDetailsNotification(): void
    {
        $this->sendNotification(new OrderDetails($this));
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
