<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Access Token
    |--------------------------------------------------------------------------
    |
    | Here we specify a unique API access token. Passing this along with API
    | requests in an authorization header will bypass user-based
    | authentication, so make sure to keep this safe. By default, it's used
    | for internally dispatched requests to the API.
    |
    | Out of the box, a random string is used here to prevent unwanted access.
    |
    */

    'token' => env('FORUM_API_TOKEN', str_random(32))

];
