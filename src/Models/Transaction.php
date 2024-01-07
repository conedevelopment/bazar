<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\TransactionFactory;
use Cone\Bazar\Events\TransactionCompleted;
use Cone\Bazar\Interfaces\Models\Transaction as Contract;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Root\Traits\InteractsWithProxy;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;
use Throwable;

class Transaction extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    public const PAYMENT = 'payment';

    public const REFUND = 'refund';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'driver_name',
        'url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'amount',
        'completed_at',
        'driver',
        'key',
        'type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_transactions';

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
        return TransactionFactory::new();
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the order for the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::getProxiedClass());
    }

    /**
     * Get the name of the gateway driver.
     */
    protected function driverName(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): ?string {
                try {
                    return Gateway::driver($attributes['driver'])->getName();
                } catch (Throwable $exception) {
                    return $attributes['driver'];
                }
            }
        );
    }

    /**
     * Get the URL of the transaction.
     */
    protected function url(): Attribute
    {
        return new Attribute(
            get: function (mixed $value, array $attributes): ?string {
                try {
                    return Gateway::driver($attributes['driver'])->getTransactionUrl($this);
                } catch (Throwable $exception) {
                    return null;
                }
            }
        );
    }

    /**
     * Determine if the payment is completed.
     */
    public function completed(): bool
    {
        return ! is_null($this->completed_at);
    }

    /**
     * Determine if the payment is pending.
     */
    public function pending(): bool
    {
        return ! $this->completed();
    }

    /**
     * Mark the transaction as completed.
     */
    public function markAsCompleted(?DateTimeInterface $date = null): void
    {
        $date = $date ?: Date::now();

        if ($this->pending() || $this->completed_at->notEqualTo($date)) {
            $this->setAttribute('completed_at', $date)->save();

            TransactionCompleted::dispatch($this);
        }
    }

    /**
     * Mark the transaction as pending.
     */
    public function markAsPending(): void
    {
        if ($this->completed()) {
            $this->setAttribute('completed_at', null)->save();
        }
    }

    /**
     * Scope the query to only include payments.
     */
    public function scopePayment(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('type'), static::PAYMENT);
    }

    /**
     * Scope the query to only include refunds.
     */
    public function scopeRefund(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('type'), static::REFUND);
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull($query->qualifyColumn('completed_at'));
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull($query->qualifyColumn('completed_at'));
    }
}
