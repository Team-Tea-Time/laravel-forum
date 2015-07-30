<?php

return [

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
        'index' => function ($parameters, $user)
        {
            return true;
        },

        'new' => [
            'index' => function ($parameters, $user)
            {
                return true;
            },
            'mark-read' => function ($parameters, $user)
            {
                return true;
            }
        ],

        'category' => [
            'index' => function ($parameters, $user)
            {
                return true;
            }
        ],

        'thread' => [
            'show' => function ($parameters, $user)
            {
                return true;
            },
            'create' => function ($parameters, $user)
            {
                return true;
            }
        ],

        'post' => [
            'show' => function ($parameters, $user)
            {
                return true;
            },
            'create' => function ($parameters, $user)
            {
                return true;
            },
            'edit' => function ($parameters, $user)
            {
                return ($parameters['post']->author == $user);
            }
        ],

        'api' => [
            'v1' => [
                'category' => [
                    'index' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'store' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'show' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'update' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'destroy' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'restore' => function ($parameters, $user)
                    {
                        return false;
                    }
                ],
                'thread' => [
                    'index' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'store' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'show' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'update' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'destroy' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'restore' => function ($parameters, $user)
                    {
                        return false;
                    }
                ],
                'post' => [
                    'index' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'store' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'show' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'update' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'destroy' => function ($parameters, $user)
                    {
                        return false;
                    },
                    'restore' => function ($parameters, $user)
                    {
                        return false;
                    }
                ]
            ]
        ]
    ],

];
