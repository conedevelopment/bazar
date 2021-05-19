<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bazar Administrators
    |--------------------------------------------------------------------------
    |
    | Here your can define administrators by their email address. By default
    | the given values are used by the 'manage-bazar' Gate defnition.
    |
    */

    'admins' => [
        'admin@bazar.test',
    ],

    /*
    |--------------------------------------------------------------------------
    | Currencies
    |--------------------------------------------------------------------------
    |
    | All the available currencies can be set here, as well as the default one.
    | The key of the currency is the ISO 4217 code, while the value is the
    | currency symbol.
    |
    */

    'currencies' => [
        'default' => strtolower(env('BAZAR_CURRENCY', 'usd')),
        'available' => [
            'usd' => 'USD',
            'eur' => 'EUR',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart Drivers
    |--------------------------------------------------------------------------
    | In this section, you can specify all the available cart drivers and
    | their configuration.
    |
    | Supported drivers: "cookie"
    |
    */

    'cart' => [
        'default' => env('BAZAR_CART_DRIVER', 'cookie'),
        'drivers' => [
            'cookie' => [
                'expiration' => 4320,
            ],
            'session' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gateway Drivers
    |--------------------------------------------------------------------------
    | In this section, you can specify all the available gateway drivers and
    | their configuration.
    |
    | Supported drivers: "cash", "transfer"
    |
    */

    'gateway' => [
        'default' => env('BAZAR_GATEWAY_DRIVER', 'transfer'),
        'drivers' => [
            'cash' => [],
            'transfer' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping Drivers
    |--------------------------------------------------------------------------
    | In this section, you can specify all the available shipping drivers and
    | their configuration.
    |
    | Supported drivers: "local-pickup"
    |
    */

    'shipping' => [
        'default' => env('BAZAR_SHIPPING_DRIVER', 'local-pickup'),
        'drivers' => [
            'local-pickup' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Units
    |--------------------------------------------------------------------------
    |
    | You can specify the weight and dimension units. A product or a variant
    | will apply the given unit on their values.
    |
    */

    'weight_unit' => env('BAZAR_WEIGHT_UNIT', 'g'),

    'dimension_unit' => env('BAZAR_DIMENSION_UNIT', 'mm'),

    /*
    |--------------------------------------------------------------------------
    | Media Settings
    |--------------------------------------------------------------------------
    |
    | You can specify the media settings here. Set the default disk to store
    | the media items. Also, you can specify the expiration of the chunks.
    |
    | Supported conversion drivers: "gd"
    |
    */

    'media' => [
        'disk' => 'public',
        'chunk_expiration' => 1440,
        'conversion' => [
            'default' => 'gd',
            'drivers' => [
                'gd' => [
                    'quality' => 70,
                ],
            ],
        ],
    ],

];
