<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Support\Countries;
use Cone\Root\Fields\Email;
use Cone\Root\Fields\MorphOne;
use Cone\Root\Fields\Select;
use Cone\Root\Fields\Text;
use Illuminate\Http\Request;

class Address extends MorphOne
{
    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Address'), $modelAttribute ?: 'address', $relation);

        $this->display('alias');
        $this->asSubResource();
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            Text::make(__('First Name'), 'first_name'),

            Text::make(__('Last Name'), 'last_name'),

            Text::make(__('Company'), 'company'),

            Text::make(__('Tax ID'), 'tax_id'),

            Email::make(__('Email'), 'email'),

            Text::make(__('Phone'), 'phone'),

            Select::make(__('Country'), 'country')
                ->options(Countries::all()),

            Text::make(__('City'), 'city'),

            Text::make(__('Postcode'), 'postcode'),

            Text::make(__('State'), 'state'),

            Text::make(__('Address'), 'address'),

            Text::make(__('Address Secondary'), 'address_secondary'),
        ];
    }
}
