<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Router
    |--------------------------------------------------------------------------
    |
    | Router config for the Livewire-based frontend.
    |
    */

    'router' => [
        'prefix' => '/forum',
        'as' => 'forum.',
        'middleware' => ['web'],
        'auth_middleware' => ['auth'],
    ],

];
