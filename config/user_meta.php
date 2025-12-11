<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Meta Fields
    |--------------------------------------------------------------------------
    | These fields are common across all clients.
    | You can enable/disable or make them optional using `required`.
    | 
    | Supported types:
    | - text       : simple string input
    | - textarea   : multi-line text
    | - number     : integer/decimal
    | - date       : date field
    | - select     : dropdown with options
    | - relation   : FK relation to another table/model
    */

    'designation' => [
        'type'     => 'select',
        'options'  => [
            'MBBS' => 'MBBS',
            'MD' => 'MD', 
            'PGDM' => 'PGDM',
            'Other' => 'Other'
        ],
        'required' => false,
        'enabled'  => false,
    ],

    'country_id' => [
        'type'     => 'relation',
        'model'    => \App\Models\Country::class,
        'required' => false,
        'enabled'  => false,
    ],

    'state_id' => [
        'type'       => 'relation',
        'model'      => \App\Models\State::class,
        'depends_on' => 'country_id',
        'required'   => false,
        'enabled'    => false,
    ],

    'date_of_birth' => [
        'type'     => 'date',
        'required' => false,
        'enabled'  => true,
    ],

    'is_pregnant' => [
        'type'     => 'integer',
        'required' => false,
        'enabled'  => true,
    ],

    'delivery_date' => [
        'type'     => 'date',
        'required' => false,
        'enabled'  => true,
    ],

    'have_siblings' => [
        'type'     => 'integer',
        'required' => false,
        'enabled'  => true,
    ]

];
