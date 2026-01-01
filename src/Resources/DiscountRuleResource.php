<?php

declare(strict_types=1);

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\DiscountRule;
use Cone\Root\Resources\Resource;

class DiscountRuleResource extends Resource
{
    /**
     * The model class.
     *
     * @var class-string<\Cone\Bazar\Interfaces\Models\DiscountRule>
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
}
