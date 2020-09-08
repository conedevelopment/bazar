<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Casts\Currency;
use Bazar\Concerns\Addressable;
use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Itemable;
use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Discountable;
use Bazar\Contracts\Shippable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Order extends Model implements Breadcrumbable, Discountable, Shippable
{
    use Addressable, BazarRoutable, Itemable, SoftDeletes;

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
        'currency' => Currency::class,
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::deleting(function (Order $order) {
            if ($order->forceDeleting) {
                $order->address()->delete();
                $order->products()->detach();
                $order->shipping()->delete();
            }
        });
    }

    /**
     * Create a new order from the given cart.
     *
     * @param  \Bazar\Models\Cart  $cart
     * @return static
     */
    public static function createFrom(Cart $cart): Order
    {
        $order = static::make($cart->toArray())->fill([
            'currency' => Bazar::currency(),
        ]);

        $order->user()->associate($cart->user)->save();

        $cart->items->each(function (Item $item) use ($order) {
            $order->products()->attach($item->product_id, $item->toArray());
        });

        $order->address()->save($cart->address);
        $order->shipping()->save($cart->shipping);
        $order->shipping->address()->save($cart->shipping->address);

        return $order;
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
     * Get the transactions for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all the payment transactions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPaymentsAttribute(): Collection
    {
        return $this->transactions->where('type', 'payment');
    }

    /**
     * Get all the refunds transactions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRefundsAttribute(): Collection
    {
        return $this->transactions->where('type', 'refund');
    }

    /**
     * Get the status name attribute.
     *
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        $statuses = static::statuses();

        return $statuses[$this->status] ?? $this->status;
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
     * @return $this
     */
    public function status(string $status): Order
    {
        $this->update(compact('status'));

        return $this;
    }

    /**
     * Get the breadcrumb label.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function getBreadcrumbLabel(Request $request): string
    {
        return "#{$this->id}";
    }

    /**
     * Scope a query to only include orders with the given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array|string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, $status): Builder
    {
        return $query->whereIn('status', (array) $status);
    }
}
