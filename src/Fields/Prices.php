<?php

namespace Cone\Bazar\Fields;

use Cone\Root\Fields\Json;
use Cone\Root\Fields\Number;
use Illuminate\Http\Request;

class Prices extends Json
{
    /**
     * Indicates of the fieldset legend is visible.
     *
     * @var bool
     */
    protected bool $withLegend = false;

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return array_merge(parent::fields($request), [
            Number::make($this->label, 'default')
                ->min(0)
                ->step(0.1),

            Number::make(sprintf('%s %s', __('Sale'), $this->label), 'sale')
                ->min(0)
                ->step(0.1),
        ]);
    }
}
