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

    'is_delivered' => [
        'type'     => 'integer',
        'required' => false,
        'enabled'  => true,
    ],

    'blood_group' => [
        'type'     => 'text',
        'required' => false,
        'enabled'  => true,
    ],

    'is_veg' => [
        'type'     => 'integer',
        'required' => false,
        'enabled'  => true,
    ],

    'husband_name' => [
        'type'     => 'text',
        'required' => false,
        'enabled'  => true,
    ],
    'preparing_to_conceive' => [
        'type'     => 'integer',
        'required' => false,
        'enabled'  => true,
    ],
    'last_period_date' => [
        'type'     => 'date',
        'required' => false,
        'enabled'  => true,
    ],
    'baby_dob' => [
        'type'     => 'date',
        'required' => false,
        'enabled'  => true,
    ],

    'meal_package_id' => [
        'type'     => 'relation',
        'model'    => \App\Models\MealPackage::class,
        'required' => false,
        'enabled'  => true,
    ],
    'deliver_breakfast_to_room' => [
        'type'     => 'integer',
        'required' => false,
        'enabled'  => true,
    ],
    'room_number' => [
        'type'     => 'text',
        'required' => false,
        'enabled'  => true,
    ],
];
