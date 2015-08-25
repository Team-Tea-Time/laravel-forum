<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable/disable routes
    |--------------------------------------------------------------------------
    |
    | Disable this if you want to effectively disable your entire forum.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Forum root
    |--------------------------------------------------------------------------
    |
    | The base URL to use for all forum routes
    |
    */

    'root' => 'forum',

    /*
    |--------------------------------------------------------------------------
    | Parameter names
    |--------------------------------------------------------------------------
    |
    | Here you can specify the parameter names to use in the forum routes. You
    | should only change these if they conflict with any route binding that
    | makes use of the same parameter names in your application.
    |
    | IMPORTANT NOTE: Changing these will change the forum API URIs. You will
    | also need to make sure the permission name keys in
    | config/forum.permissions.php reflect whatever you set here.
    |
    */

    'parameters' => [
        'category'    => 'category',
        'thread'    => 'thread',
        'post'        => 'post'
    ]

];
