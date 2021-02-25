<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Concerns\Addressable;
use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Filterable;
use Bazar\Concerns\InteractsWithDiscounts;
use Bazar\Concerns\InteractsWithItems;
use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Discountable;
use Bazar\Contracts\Itemable;
use Bazar\Contracts\Models\Cart;
use Bazar\Contracts\Models\Order as Contract;
use Bazar\Proxies\Transaction as TransactionProxy;
use Bazar\Proxies\User as UserProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Order extends Model implements Breadcrumbable, Contract, Discountable, Itemable
{
    use Addressable, BazarRoutable, Filterable, InteractsWithDiscounts, InteractsWithItems, SoftDeletes;

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
     * Create a new order from the given cart.
     *
     * @param  \Bazar\Contracts\Models\Cart  $cart
     * @return static
     */
    public static function createFrom(Cart $cart): Order
    {
        $order = static::make($cart->toArray());

        $order->user()->associate($cart->user)->save();

        $order->products()->attach(
            $cart->items->mapWithKeys(static function (Item $item): array {
                return [$item->product_id => $item->only([
                    'price', 'tax', 'quantity', 'properties',
                ])];
            })->toArray()
        );

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
     * Get the filter options for the model.
     *
     * @return array
     */
    public static function filters(): array
    {
        return [
            'state' => [
                'all' => __('All'),
                'available' => __('Available'),
                'trashed' => __('Trashed')
            ],
            'status' => static::statuses(),
            'user' => UserProxy::query()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Get the transactions for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(TransactionProxy::getProxiedClass());
    }

    /**
     * Get the payments attribute.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPaymentsAttribute(): Collection
    {
        return $this->transactions->where('type', 'payment');
    }

    /**
     * Get the refunds attribute.
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
    public function status(string $status): Contract
    {
        $this->update(compact('status'));

        return $this;
    }

    /**
     * Get the breadcrumb representation of the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function toBreadcrumb(Request $request): string
    {
        return "#{$this->id}";
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
}
