<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation rules
    |--------------------------------------------------------------------------
    |
    | Here we define validation rules for all of the fields in the package.
    | These are split up into two parts: the base rules for validating the
    | contents of fields, and rules specific to the request type. The
    | appropriate ruleset is used along with the base ones depending
    | on the type of request being made at the time of validation.
    |
    */

    'rules' => [
        'base' => [
            'author_id'             => ['integer'],
            'category_id'           => ['integer', 'exists:forum_categories,id'],
            'content'               => ['min:5'],
            'locked'                => ['boolean'],
            'pinned'                => ['boolean'],
            'subtitle'              => ['string', 'min:5'],
            'thread_id'             => ['integer', 'exists:forum_threads,id'],
            'title'                 => ['string', 'min:5'],
            'weight'                => ['integer']
        ],
        'create' => [
            'category' => [
                'title'             => ['required']
            ],
            'thread' => [
                'title'             => ['required']
            ],
            'post' => [
                'content'           => ['required']
            ]
        ],
        'update' => [
            'category' => [
                'category_id'       => ['required_without_all:title,subtitle,weight,allows_threads'],
                'title'             => ['required_without_all:category_id,subtitle,weight,allows_threads'],
                'subtitle'          => ['required_without_all:category_id,title,weight,allows_threads'],
                'weight'            => ['required_without_all:category_id,title,subtitle,allows_threads'],
                'allows_threads'    => ['required_without_all:category_id,title,subtitle,weight']
            ],
            'thread' => [
                'category_id'       => ['required_without_all:author_id,title,locked,pinned'],
                'author_id'         => ['required_without_all:category_id,title,locked,pinned'],
                'title'             => ['required_without_all:category_id,author_id,locked,pinned'],
                'locked'            => ['required_without_all:category_id,author_id,title,pinned'],
                'pinned'            => ['required_without_all:category_id,author_id,title,locked']
            ],
            'post' => [
                'category_id'       => ['required_without_all:author_id,content'],
                'author_id'         => ['required_without_all:category_id,content'],
                'content'           => ['required_without_all:category_id,author_id']
            ]
        ]
    ]

];
