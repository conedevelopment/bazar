<?php

declare(strict_types=1);

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\DiscountRule;
use Cone\Root\Fields\ID;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DiscountRuleResource extends Resource
{
    /**
     * The model class.
     *
     * @var class-string<\Cone\Bazar\Models\DiscountRule>
     */
    protected static string $model = DiscountRule::class;

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
        ];
    }
}
