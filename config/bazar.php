<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The Bazar Administrators
    |--------------------------------------------------------------------------
    |
    | Bazar does not handle roles or different permissions. The users whom are
    | defined here will have access to the complete admin section. By
    | default their email address is used for authorization.
    |
    */

    'admins' => [
        'admin@bazar.test',
    ],

    /*
    |--------------------------------------------------------------------------
    | The Currencies
    |--------------------------------------------------------------------------
    |
    | All the available currencies can be set here, as well as the default one.
    | The available currencies will be shown on the products admin page as
    | a configurable price. The key of the currency is the ISO code,
    | while the value is the currency symbol.
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
    | The Cart Drivers
    |--------------------------------------------------------------------------
    | In this section, you can specify the cart driver that you want to use
    | to resolve the cart instance, that is used by the cart facade.
    |
    | Supported drivers: "cookie"
    |
    */

    'cart' => [
        'default' => 'cookie',
        'reservation' => 60,
        'drivers' => [
            'cookie' => [
                'expiration' => 4320,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | The Gateway Drivers
    |--------------------------------------------------------------------------
    | In this section, you can specify the gateway driver that you want to use
    | to resolve the gateway instance, that is used by the gateway facade.
    |
    | Supported drivers: "cash", "manual", "transfer"
    |
    */

    'gateway' => [
        'default' => 'transfer',
        'drivers' => [
            'cash' => [],
            'manual' => [],
            'transfer' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | The Shipping Drivers
    |--------------------------------------------------------------------------
    | In this section, you can specify the shipping driver that you want to use
    | to resolve the shipping instance, that is used by the shipping facade.
    |
    | Supported drivers: "local-pickup", "weight-based-shipping"
    |
    */

    'shipping' => [
        'default' => 'local-pickup',
        'drivers' => [
            'local-pickup' => [],
            'weight-based-shipping' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Weight Unit
    |--------------------------------------------------------------------------
    |
    | You can specify the weight unit here. When a product's weight will be
    | specified it will apply this unit.
    |
    */

    'weight_unit' => env('BAZAR_WEIGHT_UNIT', 'g'),

    /*
    |--------------------------------------------------------------------------
    | Dimension Unit
    |--------------------------------------------------------------------------
    |
    | You can specify the dimension unit here. When a product's dimension will be
    | specified it will apply this unit.
    |
    */

    'dimension_unit' => env('BAZAR_DIMENSION_UNIT', 'mm'),

    /*
    |--------------------------------------------------------------------------
    | Media Settings
    |--------------------------------------------------------------------------
    |
    | You can specify the media settings here. Set the default disk to store
    | the media items. Also, you can specify the expiration of the chunks.
    |
    */

    'media' => [
        'disk' => 'public',
        'chunk_expiration' => 1440,
    ],

];
