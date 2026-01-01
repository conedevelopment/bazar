<?php

declare(strict_types=1);

namespace Cone\Bazar\Resources;

use Cone\Bazar\Enums\DiscountRuleType;
use Cone\Bazar\Models\DiscountRule;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\ID;
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
        return $model->name;
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
                ->rules(['required', 'string', 'max:255']),

            Boolean::make(__('Active'), 'active')
                ->sortable()
                ->rules(['required', 'boolean']),

            Select::make(__('Type'), 'type')
                ->options(DiscountRuleType::toArray())
                ->sortable()
                ->rules(['required', 'string', Rule::in(array_column(DiscountRuleType::cases(), 'value'))]),

            Boolean::make(__('Stackable'), 'stackable')
                ->sortable()
                ->rules(['required', 'boolean']),

            BelongsToMany::make(__('Users'), 'users')
                ->searchable(columns: ['name', 'email'])
                ->async()
                ->display('name'),
        ];
    }
}
