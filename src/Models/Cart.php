<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Bazar;
use Cone\Bazar\Database\Factories\CartFactory;
use Cone\Bazar\Enums\Currency;
use Cone\Bazar\Exceptions\CartException;
use Cone\Bazar\Interfaces\Models\Cart as Contract;
use Cone\Bazar\Traits\Addressable;
use Cone\Bazar\Traits\AsOrder;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Throwable;

class Cart extends Model implements Contract
{
    use Addressable;
    use AsOrder;
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'currency' => null,
        'locked' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'currency',
        'locked',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_carts';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct(array_merge(
            ['currency' => Bazar::getCurrency()],
            $attributes
        ));
    }

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
    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'currency' => Currency::class,
            'locked' => 'bool',
        ];
    }

    /**
     * Get the order for the cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Cone\Bazar\Models\Order, \Cone\Bazar\Models\Cart>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::getProxiedClass())
            ->withDefault(function (Order $order): Order {
                return $order->fill($this->toArray());
            });
    }

    /**
     * Get the address for the model.
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::getProxiedClass(), 'addressable')
            ->withDefault(function (Address $address): Address {
                return $address->fill($this->user?->address?->toArray() ?: []);
            });
    }

    /**
     * Lock the cart.
     */
    public function lock(): void
    {
        if (! $this->locked) {
            $this->setAttribute('locked', true)->save();
        }
    }

    /**
     * Unlock the cart.
     */
    public function unlock(): void
    {
        if ($this->locked) {
            $this->setAttribute('locked', false)->save();
        }
    }

    /**
     * Scope a query to only include the locked carts.
     */
    #[Scope]
    protected function locked(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('locked'), true);
    }

    /**
     * Scope a query to only include the unlocked carts.
     */
    #[Scope]
    protected function unlocked(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('locked'), false);
    }

    /**
     * Scope a query to only include the expired carts.
     */
    #[Scope]
    protected function expired(Builder $query): Builder
    {
        return $query->whereNull($query->qualifyColumn('user_id'))
            ->where($query->qualifyColumn('updated_at'), '<', Date::now()->subDays(3));
    }

    /**
     * Convert the cart to a new order.
     */
    public function toOrder(): Order
    {
        $this->getLineItems()->each(function (Item $item): void {
            if (! $item->buyable->buyable($this->order)) {
                throw new CartException(sprintf('Unable to add [%s] item to the order.', get_class($item->buyable)));
            }
        });

        try {
            DB::transaction(function (): void {
                $this->order->user()->associate($this->user)->save();

                if ($this->order_id !== $this->order->getKey()) {
                    $this->order()->associate($this->order)->save();
                }

                $this->order->items()->delete();
                $this->order->items()->createMany($this->items->toArray());

                $this->order->address->fill($this->address->toArray())->save();

                if ($this->order->needsShipping()) {
                    $this->order->shipping->fill($this->shipping->toArray())->save();
                    $this->order->shipping->address->fill($this->shipping->address->toArray())->save();
                }

                $this->coupons->each(function (Coupon $coupon): void {
                    $this->order->applyCoupon($coupon);
                });

                $this->order->calculateTax();
            });
        } catch (Throwable $exception) {
            //
        }

        return $this->order;
    }
}
