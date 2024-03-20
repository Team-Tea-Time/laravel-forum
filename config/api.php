<?php

return [

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
        'namespace' => '\\TeamTeaTime\\Forum\\Http\\Controllers\\Api',
        'middleware' => ['api', 'auth:api'],
        'auth_middleware' => ['auth:api'],
    ],

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
    | Resources
    |--------------------------------------------------------------------------
    |
    | Override to return your own resources for API responses.
    |
    */

    'resources' => [
        'category' => TeamTeaTime\Forum\Http\Resources\CategoryResource::class,
        'thread' => TeamTeaTime\Forum\Http\Resources\ThreadResource::class,
        'post' => TeamTeaTime\Forum\Http\Resources\PostResource::class,
    ],

];
