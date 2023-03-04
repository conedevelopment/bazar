<?php

namespace Cone\Bazar\Fields;

use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\Json;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;

class Inventory extends Json
{
    /**
     * {@inheritdoc}
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(parent::fields($request), [
            Text::make(__('SKU'), 'sku'),
            Number::make(__('Quantity'), 'quantity'),
            Number::make(__('Width'), 'width'),
            Number::make(__('Height'), 'height'),
            Number::make(__('Length'), 'length'),
            Number::make(__('Weight'), 'weight'),
            Boolean::make(__('Virtual'), 'virtual'),
            Boolean::make(__('Downloadable'), 'downloadable'),
        ]);
    }
}
