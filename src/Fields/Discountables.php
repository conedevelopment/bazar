<?php

declare(strict_types=1);

namespace Cone\Bazar\Fields;

use Cone\Root\Fields\MorphToMany;

class Discountables extends MorphToMany
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        return MorphToMany::make(__('Discountables'), 'discountables')
            ->display(function (Model $model): string {
                return match (true) {
                    /* $model instanceof Product => $model->getBuyableName(),
                    $model instanceof Variant => $model->getBuyableName(), */
                    default => (string) $model->getKey(),
                };
            });
    }
}
