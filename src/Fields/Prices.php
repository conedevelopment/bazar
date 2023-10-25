<?php

namespace Cone\Bazar\Fields;

use Cone\Root\Fields\Fieldset;
use Cone\Root\Fields\Meta;
use Illuminate\Http\Request;

class Prices extends Fieldset
{
    /**
     * Create a new prices field instance.
     */
    public function __construct(string $label = null, string $modelAttribute = null)
    {
        parent::__construct($label ?: __('Prices'), $modelAttribute);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            Meta::make(__('Price :currency', ['currency' => 'HUF']), 'price_huf')
                ->asNumber(),
        ];
    }
}
