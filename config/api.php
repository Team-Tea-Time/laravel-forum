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
    | Enable/disable search
    |--------------------------------------------------------------------------
    |
    | Whether or not to enable the post search endpoint.
    |
    */

    'enable_search' => true,

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
        'middleware' => ['api', 'auth:api'],
    ],

];
