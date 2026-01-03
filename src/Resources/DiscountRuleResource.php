<?php

declare(strict_types=1);

namespace Cone\Bazar\Resources;

use Cone\Bazar\Enums\DiscountRuleValueType;
use Cone\Bazar\Enums\DiscountType;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\DiscountRule;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Models\Variant;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Repeater;
use Cone\Root\Fields\Select;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountRuleResource extends Resource
{
    /**
     * The model class.
     *
     * @var class-string<\Cone\Bazar\Models\DiscountRule>
     */
    protected string $model = DiscountRule::class;

    /**
     * The group for the resource.
     */
    protected string $group = 'Shop';

    /**
     * Get the model for the resource.
     */
    public function getModel(): string
    {
        return $this->model::getProxiedClass();
    }

    /**
     * {@inheritdoc}
     */
    public function modelTitle(Model $model): string
    {
        return $model->name ?: parent::modelTitle($model);
    }

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Name'), 'name')
                ->sortable()
                ->searchable()
                ->hydratesOnChange()
                ->rules(['required', 'string', 'max:255']),

            Boolean::make(__('Active'), 'active')
                ->sortable()
                ->rules(['boolean']),

            Boolean::make(__('Stackable'), 'stackable')
                ->sortable()
                ->rules(['boolean']),

            BelongsToMany::make(__('Users'), 'users')
                ->searchable(columns: ['name', 'email'])
                ->async()
                ->display('name'),

            Select::make(__('Discountable Type'), 'discountable_type')
                ->options([
                    __('Cart') => [
                        Cart::getProxiedClass() => __('Cart'),
                        Shipping::getProxiedClass() => __('Shipping'),
                    ],
                    __('Buyable Item') => [
                        Product::getProxiedClass() => __('Product'),
                        Variant::getProxiedClass() => __('Variant'),
                    ],
                ])
                ->sortable()
                ->rules(['required', 'string'])
                ->hydratesOnChange(),

            Select::make(__('Value Type'), 'value_type')
                ->options(DiscountRuleValueType::toArray())
                ->sortable()
                ->rules(['required', 'string', Rule::in(array_column(DiscountRuleValueType::cases(), 'value'))])
                ->hydratesOnChange(),

            Repeater::make(__('Rules'), 'rules->conditions')
                ->withFields(static function (Request $request): array {
                    return [
                        Number::make(__('Value'), 'value')
                            ->rules(['required', 'numeric', 'min:0']),

                        Select::make(__('Type'), 'type')
                            ->options(DiscountType::toArray())
                            ->required()
                            ->rules(['required', Rule::in(array_column(DiscountType::cases(), 'value'))]),

                        Number::make(__('Discount'), 'discount')
                            ->rules(['required', 'numeric', 'min:0']),
                    ];
                }),
        ];
    }
}
