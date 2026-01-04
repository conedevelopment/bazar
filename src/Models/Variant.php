<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\VariantFactory;
use Cone\Bazar\Enums\Currency;
use Cone\Bazar\Interfaces\Checkoutable;
use Cone\Bazar\Interfaces\Models\Variant as Contract;
use Cone\Bazar\Traits\HasPrices;
use Cone\Bazar\Traits\HasProperties;
use Cone\Bazar\Traits\InteractsWithCheckoutables;
use Cone\Bazar\Traits\InteractsWithStock;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\HasMetaData;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model implements Contract
{
    use HasFactory;
    use HasMedia;
    use HasMetaData;
    use HasPrices {
        HasPrices::getPrice as __getPrice;
    }
    use HasProperties;
    use InteractsWithCheckoutables;
    use InteractsWithProxy;
    use InteractsWithStock;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alias',
        'description',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_variants';

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
    protected static function newFactory(): VariantFactory
    {
        return VariantFactory::new();
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the product for the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::getProxiedClass())
            ->withDefault();
    }

    /**
     * Get the tax rates for the product.
     */
    public function taxRates(): MorphToMany
    {
        return $this->product->taxRates();
    }

    /**
     * Get the name attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return new Attribute(
            get: fn (): string => sprintf('%s - %s', $this->product->name, $this->alias)
        );
    }

    /**
     * Get the alias attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function alias(): Attribute
    {
        return new Attribute(
            get: function (?string $value): ?string {
                return $this->exists ? ($value ?: "#{$this->getKey()}") : $value;
            }
        );
    }

    /**
     * Determine whether the buyable object is available for the checkoutable instance.
     */
    public function buyable(Checkoutable $checkoutable): bool
    {
        return $this->product->buyable($checkoutable);
    }

    /**
     * Get the name of the buyable instance.
     */
    public function getBuyableName(): string
    {
        return $this->name;
    }

    /**
     * Get the price by the given type and currency.
     */
    public function getPrice(?Currency $currency = null): ?float
    {
        return $this->__getPrice($currency) ?: $this->product->getPrice($currency);
    }

    /**
     * Get the item representation of the buyable instance.
     */
    public function toItem(Checkoutable $checkoutable, array $attributes = []): Item
    {
        return $this->items()->make(array_merge([
            'name' => $this->getBuyableName(),
            'price' => $this->getPrice($checkoutable->getCurrency()),
            'quantity' => 1,
        ], $attributes))->setRelation('buyable', $this);
    }
}
