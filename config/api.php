<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable/disable
    |--------------------------------------------------------------------------
    |
    | Set to false if you want to effectively disable the API.
    |
    */

    'enable' => true,

    /*
    |--------------------------------------------------------------------------
    | Router
    |--------------------------------------------------------------------------
    |
    | API router config.
    |
    */

    'router' => [
        'prefix' => '/forum/api',
        'as' => 'forum.api.',
        'namespace' => '\TeamTeaTime\Forum\Http\Controllers\Api',
        'middleware' => ['api', 'auth:api']
    ],

];
