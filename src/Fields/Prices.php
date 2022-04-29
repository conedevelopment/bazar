<?php

namespace Cone\Bazar\Fields;

use Cone\Root\Fields\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Prices extends Field
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Prices';

    /**
     * Format the value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = static function (Request $request, Model $model): ?string {
                return $model->formattedPrice;
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
