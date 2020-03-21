<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable/disable
    |--------------------------------------------------------------------------
    |
    | Set to false if you want to effectively disable the frontend.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Router
    |--------------------------------------------------------------------------
    |
    | Frontend router config.
    |
    */

    'router' => [
        'prefix' => '/forum',
        'as' => 'forum.',
        'namespace' => '\TeamTeaTime\Forum\Http\Controllers\Frontend',
        'middleware' => ['web']
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Prefixes
    |--------------------------------------------------------------------------
    |
    | Prefixes to use for each model.
    |
    */

    'route_prefixes' => [
        'category' => 'c',
        'thread' => 't',
        'post' => 'p',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Category Color
    |--------------------------------------------------------------------------
    |
    | The default color to use when creating new categories.
    |
    */

    'default_category_color' => '#007bff',

    /*
    |--------------------------------------------------------------------------
    | Utility Class
    |--------------------------------------------------------------------------
    |
    | Here we specify the class to use for various frontend utility methods.
    | This is automatically aliased to 'Forum' for ease of use in views.
    |
    */

    'utility_class' => TeamTeaTime\Forum\Support\Frontend\Forum::class,

];
