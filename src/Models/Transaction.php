<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Concerns\InteractsWithProxy;
use Cone\Bazar\Contracts\Models\Transaction as Contract;
use Cone\Bazar\Database\Factories\TransactionFactory;
use Cone\Bazar\Support\Facades\Gateway;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;
use Throwable;

class Transaction extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The payment type.
     *
     * @var string
     */
    public const PAYMENT = 'payment';

    /**
     * The refund type.
     *
     * @var string
     */
    public const REFUND = 'refund';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url',
        'driver_name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'type',
        'amount',
        'driver',
        'completed_at',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_transactions';

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
     * @return \Cone\Bazar\Database\Factories\TransactionFactory
     */
    protected static function newFactory(): TransactionFactory
    {
        return TransactionFactory::new();
    }

    /**
     * Get the order for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::getProxiedClass());
    }

    /**
     * Get the name of the gateway driver.
     *
     * @return string
     */
    public function getDriverNameAttribute(): string
    {
        try {
            return Gateway::driver($this->driver)->getName();
        } catch (Throwable $exception) {
            return $this->driver;
        }
    }

    /**
     * Get the URL of the transaction.
     *
     * @return string|null
     */
    public function getUrlAttribute(): ?string
    {
        try {
            return Gateway::driver($this->driver)->getTransactionUrl($this);
        } catch (Throwable $exception) {
            return null;
        }
    }

    /**
     * Determine if the payment is completed.
     *
     * @return bool
     */
    public function completed(): bool
    {
        return ! is_null($this->completed_at);
    }

    /**
     * Determine if the payment is pending.
     *
     * @return bool
     */
    public function pending(): bool
    {
        return ! $this->completed();
    }

    /**
     * Mark the transaction as completed.
     *
     * @param \DateTimeInterface|null  $date
     * @return void
     */
    public function markAsCompleted(?DateTimeInterface $date = null): void
    {
        $date = $date ?: Date::now();

        if ($this->pending() || $this->completed_at->notEqualTo($date)) {
            $this->setAttribute('completed_at', $date)->save();
        }
    }

    /**
     * Mark the transaction as pending.
     *
     * @return void
     */
    public function markAsPending(): void
    {
        if ($this->completed()) {
            $this->setAttribute('completed_at', null)->save();
        }
    }

    /**
     * Scope the query to only include payments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePayment(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('type'), static::PAYMENT);
    }

    /**
     * Scope the query to only include refunds.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRefund(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('type'), static::REFUND);
    }

    /**
     * Scope a query to only include completed transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull($query->qualifyColumn('completed_at'));
    }

    /**
     * Scope a query to only include pending transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull($query->qualifyColumn('completed_at'));
    }
}
