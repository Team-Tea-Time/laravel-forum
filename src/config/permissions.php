<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable/disable permissions
    |--------------------------------------------------------------------------
    |
    | You can disable the built-in forum permission checking here if you want
    | to implement it yourself through some other means.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Permission handling method
    |--------------------------------------------------------------------------
    |
    | Here we specify the method to use for handling permissions. It defaults
    | to 'callback', which refers to the permission callbacks listed below.
    | Set to 'class' if you'd rather specify a class to handle this.
    |
    */

    'method' => 'callback',

    /*
    |--------------------------------------------------------------------------
    | Permission aliases
    |--------------------------------------------------------------------------
    |
    | Here we specify which permissions should be aliased to other ones. As
    | permissions are based on route names, by default, we map the POST and
    | PATCH permission names to their respective GET permissions, as well as
    | permissions for equivalent API requests.
    |
    */

    'aliases' => [
        'thread.store'                => 'thread.create',
        'thread.update'               => 'thread.edit',
        'post.store'                  => 'post.create',
        'post.update'                 => 'post.edit',
        'api.v1.bulk.thread.lock'     => 'api.v1.thread.lock',
        'api.v1.bulk.thread.pin'      => 'api.v1.thread.pin',
        'api.v1.bulk.thread.destroy'  => 'api.v1.thread.destroy',
        'api.v1.bulk.thread.restore'  => 'api.v1.thread.restore',
        'api.v1.bulk.post.destroy'    => 'api.v1.post.destroy',
        'api.v1.bulk.post.restore'    => 'api.v1.post.restore'
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission handling method: class
    |--------------------------------------------------------------------------
    |
    | If 'method' is set to 'class', specify the name of the class here. The
    | class handle() method will receive the permission name as well as the
    | parameters and user.
    |
    */

    'class' => null,

    /*
    |--------------------------------------------------------------------------
    | Permission handling method: callback
    |--------------------------------------------------------------------------
    |
    | If 'method' is set to 'callback', the functions here are used to handle
    | permissions.
    |
    */

    'callback' => [
        'index' => function ($user, $parameters = [])
        {
            return true;
        },

        'new' => [
            'index' => function ($user, $parameters = [])
            {
                return true;
            },
            'mark-read' => function ($user, $parameters = [])
            {
                return true;
            }
        ],

        'category' => [
            'index' => function ($user, $parameters = [])
            {
                return true;
            }
        ],

        'thread' => [
            'show' => function ($user, $parameters = [])
            {
                return true;
            },
            'create' => function ($user, $parameters = [])
            {
                return true;
            }
        ],

        'post' => [
            'show' => function ($user, $parameters = [])
            {
                return true;
            },
            'create' => function ($user, $parameters = [])
            {
                return true;
            },
            'edit' => function ($user, $parameters = [])
            {
                return ($parameters['post']->author == $user);
            }
        ],

        'api' => [
            'v1' => [
                'category' => [
                    'index' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'store' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'show' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'update' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'destroy' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'restore' => function ($user, $parameters = [])
                    {
                        return false;
                    }
                ],
                'thread' => [
                    'index' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'store' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'show' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'update' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'destroy' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'restore' => function ($user, $parameters = [])
                    {
                        return false;
                    }
                ],
                'post' => [
                    'index' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'store' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'show' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'update' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'destroy' => function ($user, $parameters = [])
                    {
                        return false;
                    },
                    'restore' => function ($user, $parameters = [])
                    {
                        return false;
                    }
                ]
            ]
        ]
    ],

];
