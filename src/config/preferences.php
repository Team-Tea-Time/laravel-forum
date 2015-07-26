<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Thread settings
    |--------------------------------------------------------------------------
    */

    'thread' => [
        // Cut-off age:
        // Specify the minimum age of a thread before it should be considered
        // old. This determines whether or not a thread can be considered new or
        // unread for any logged in user. Setting a longer cut-off duration here
        // will increase the size of your forum_threads_read table.
        // Must be a valid strtotime() string, or set to false to completely
        // disable age-sensitive thread features.
        'cutoff_age' => '-1 month'
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination settings
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'categories'    => 20, // Categories per page (only applies to the API)
        'threads'       => 20, // Threads per page
        'posts'         => 15  // Posts per page
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation settings
    |--------------------------------------------------------------------------
    |
    | Here we define validation rules for all of the fields in the package.
    | These are split up into two parts: the base rules for validating the
    | contents of fields, and rules specific to the request type. The
    | appropriate ruleset is used along with the base ones depending
    | on the type of request being made at the time of validation.
    |
    */

    'validation' => [
        'base' => [
            'author_id'         => ['integer'],
            'category_id'       => ['integer', 'exists:forum_categories,id'],
            'content'           => ['min:5'],
            'locked'            => ['boolean'],
            'pinned'            => ['boolean'],
            'subtitle'          => ['string', 'min:5'],
            'thread_id'         => ['integer', 'exists:forum_threads,id'],
            'title'             => ['string', 'min:5'],
            'weight'            => ['integer']
        ],
        'post|put' => [
            'category' => [
                'title'         => ['required']
            ],
            'thread' => [
                'category_id'   => ['required'],
                'author_id'     => ['required'],
                'title'         => ['required']
            ],
            'post' => [
                'thread_id'     => ['required'],
                'author_id'     => ['required'],
                'content'       => ['required']
            ]
        ],
        'patch' => [
            'category' => [
                'category_id'   => ['required_without_all:title,subtitle,weight'],
                'title'         => ['required_without_all:category_id,subtitle,weight'],
                'subtitle'      => ['required_without_all:category_id,title,weight'],
                'weight'        => ['required_without_all:category_id,title,subtitle']
            ],
            'thread' => [
                'category_id'   => ['required_without_all:author_id,title,locked,pinned'],
                'author_id'     => ['required_without_all:category_id,title,locked,pinned'],
                'title'         => ['required_without_all:category_id,author_id,locked,pinned'],
                'locked'        => ['required_without_all:category_id,author_id,title,pinned'],
                'pinned'        => ['required_without_all:category_id,author_id,title,locked']
            ],
            'post' => [
                'category_id'   => ['required_without_all:author_id,content'],
                'author_id'     => ['required_without_all:category_id,content'],
                'content'       => ['required_without_all:category_id,author_id']
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Misc settings
    |--------------------------------------------------------------------------
    */

    'misc'  => [
        // Soft Delete: disable this if you want categories, threads and posts
        // to be permanently removed from your database when they're deleted.
        'soft_delete' => true
    ]

];
