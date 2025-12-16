<?php

declare(strict_types=1);

namespace Cone\Bazar\Resources;

use Cone\Bazar\Enums\CouponType;
use Cone\Bazar\Models\Coupon;
use Cone\Bazar\Models\Product;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\Fieldset;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Select;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Coupon::class;

    /**
     * The group for the resource.
     */
    protected string $group = 'Shop';

    /**
     * The relations to eager load on every query.
     */
    protected array $withCount = [
        'applications',
    ];

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
        return $model->code;
    }

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Code'), 'code')
                ->rules(['required', 'string', 'max:256'])
                ->searchable()
                ->sortable()
                ->required(),

            Select::make(__('Type'), 'type')
                ->options(CouponType::toArray())
                ->required()
                ->rules(['required', Rule::in(array_column(CouponType::cases(), 'value'))]),

            Number::make(__('Value'), 'value')
                ->required()
                ->rules(['required', 'numeric', 'min:0'])
                ->step(0.1)
                ->min(0)
                ->format(static function (Request $request, Model $model): string {
                    return (string) match ($model->type) {
                        CouponType::PERCENT => $model->value.'%',
                        default => $model->value,
                    };
                }),

            Boolean::make(__('Active'), 'active')
                ->sortable(),

            Date::make(__('Expires At'), 'expires_at')
                ->withTime()
                ->sortable(),

            Boolean::make(__('Stackable'), 'stackable')
                ->sortable(),

            Fieldset::make(__('Rules'), 'rules')
                ->withFields(static function (): array {
                    return [
                        Number::make(__('Limit'), 'rules->limit')
                            ->min(0)
                            ->rules(['nullable', 'numeric', 'min:0']),

                        Select::make(__('Products'), 'rules->products')
                            ->multiple()
                            ->options(function (): array {
                                return Product::proxy()
                                    ->query()
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->rules(['nullable', 'array']),
                    ];
                }),
        ];
    }
}
