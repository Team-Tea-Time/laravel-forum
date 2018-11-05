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
        'thread_prefix' => 't',
        'category_prefix' => 'c',
        'namespace' => '\Riari\Forum\HTTP\Controllers\Frontend',
        'middleware' => ['web']
    ],

    /*
    |--------------------------------------------------------------------------
    | Utility Class
    |--------------------------------------------------------------------------
    |
    | Here we specify the class to use for various frontend utility methods.
    | This is automatically aliased to 'Forum' for ease of use in views.
    |
    */

    'utility_class' => Riari\Forum\Support\Frontend\Forum::class,

];
