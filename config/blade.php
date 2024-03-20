<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Router
    |--------------------------------------------------------------------------
    |
    | Router config for the Blade-based frontend.
    |
    */

    'router' => [
        'prefix' => '/forum',
        'as' => 'forum.',
        'namespace' => '\\TeamTeaTime\\Forum\\Http\\Controllers\\Blade',
        'middleware' => ['web'],
        'auth_middleware' => ['auth'],
    ],

];
