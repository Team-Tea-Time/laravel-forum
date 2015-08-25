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
        'thread.store'              => 'thread.create',
        'thread.update'             => 'thread.edit',
        'post.store'                => 'post.create',
        'post.update'               => 'post.edit',
        'api.bulk.category.update'  => 'api.category.update',
        'api.bulk.category.destroy' => 'api.category.destroy',
        'api.bulk.category.restore' => 'api.category.destroy',
        'api.category.restore'      => 'api.category.destroy',
        'api.bulk.thread.update'    => 'api.thread.update',
        'api.bulk.thread.destroy'   => 'api.thread.destroy',
        'api.bulk.thread.restore'   => 'api.thread.destroy',
        'api.thread.restore'        => 'api.thread.destroy',
        'api.bulk.post.update'      => 'api.post.update',
        'api.bulk.post.destroy'     => 'api.post.destroy',
        'api.bulk.post.restore'     => 'api.post.destroy',
        'api.post.restore'          => 'api.post.destroy',
    ],

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
                }
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback: access denied
    |--------------------------------------------------------------------------
    |
    | This function is called when a permission check fails in the
    | CheckPermission middleware. Does not apply to API routes.
    |
    */

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    'access_denied' => function ($request)
    {
        return abort(403);
    },

];
