<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission aliases
    |--------------------------------------------------------------------------
    |
    | Here we specify which permissions should be aliased to other ones. As
    | permissions are based on route names, by default, we map the POST and
    | PATCH permission names to their respective GET permissions.
    |
    */

    'aliases' => [
        'forum.thread.store'    => 'forum.thread.create',
        'forum.thread.update'   => 'forum.thread.edit',
        'forum.post.store'      => 'forum.post.create',
        'forum.post.update'     => 'forum.post.edit'
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission callbacks
    |--------------------------------------------------------------------------
    |
    | Here we define callbacks for the forum permissions. All of these map to
    | a named forum route, and each one receives an array of paramaters as well
    | as the current user.
    |
    */

    'forum' => [
        'index' => function ($parameters, $user)
        {
            return true;
        },

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
            },
            'lock' => function ($parameters, $user)
            {
                return false;
            },
            'pin' => function ($parameters, $user)
            {
                return false;
            },
            'delete' => function ($parameters, $user)
            {
                return false;
            }
        ],

        'post' => [
            'create' => function ($parameters, $user)
            {
                return true;
            },
            'edit' => function ($parameters, $user)
            {
                return ($parameters['post']->author == $user);
            },
            'delete' => function ($parameters, $user)
            {
                return false;
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
                    }
                ]
            ]
        ]
    ],

];
