<?php

declare(strict_types=1);

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Enums\DiscountRuleValueType;
use Cone\Bazar\Enums\DiscountType;
use Cone\Bazar\Interfaces\Buyable;
use Cone\Bazar\Models\DiscountRule;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Variant;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\MorphToMany;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Repeater;
use Cone\Root\Fields\Select;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                ->options(array_combine(
                    $this->getModel()::getDiscountableTypes(),
                    array_map(static function (string $type): string {
                        return __(Str::of($type)->classBasename()->value());
                    }, $this->getModel()::getDiscountableTypes()),
                ))
                ->sortable()
                ->rules(['required', 'string'])
                ->hydratesOnChange(),

            MorphToMany::make(__('Discountables'), 'discountables')
                ->hiddenOn(['index'])
                ->withRelatableQuery(static function (Request $request, Builder $query, Model $model): Builder {
                    return match (true) {
                        $query->getModel() instanceof Product,
                        $query->getModel() instanceof Variant => $query,
                        default => $query->whereRaw('1 = 0'),
                    };
                })
                ->display(static function (Model $model): string {
                    return match (true) {
                        $model instanceof Buyable => sprintf('#%s - %s', (string) $model->getKey(), $model->getBuyableName()),
                        default => (string) $model->getKey(),
                    };
                }),

            Select::make(__('Value Type'), 'value_type')
                ->options(DiscountRuleValueType::toArray())
                ->sortable()
                ->rules(['required', 'string', Rule::in(array_column(DiscountRuleValueType::cases(), 'value'))]),

            Repeater::make(__('Rules'), 'rules')
                ->withFields(static function (): array {
                    return [
                        Number::make(__('Value'), 'value')
                            ->required()
                            ->rules(['required', 'numeric', 'min:0']),

                        Select::make(__('Type'), 'type')
                            ->options(DiscountType::toArray())
                            ->required()
                            ->rules(['required', Rule::in(array_column(DiscountType::cases(), 'value'))]),

                        Number::make(__('Discount'), 'discount')
                            ->required()
                            ->rules(['required', 'numeric', 'min:0']),

                        Select::make(__('Currency'), 'currency')
                            ->nullable()
                            ->options(array_column(Bazar::getCurrencies(), 'name', 'value'))
                            ->format(static function (Request $request, Model $model, ?string $value): string {
                                return (string) ($value ?: '*');
                            }),
                    ];
                }),
        ];
    }
}
