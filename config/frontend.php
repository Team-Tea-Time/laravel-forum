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
        'namespace' => '\Riari\Forum\HTTP\Controllers\Frontend',
        'middleware' => []
    ],

];
