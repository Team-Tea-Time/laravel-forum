<?php

return [

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
        ],

        'api' => [
            'v1' => [
                'category' => [
                    'index' => function ($parameters, $user)
                    {
                        return true;
                    }
                ]
            ]
        ]
    ],

];
