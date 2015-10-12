<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Old thread threshold
    |--------------------------------------------------------------------------
    |
    | The minimum age of a thread before it should be considered old. This
    | determines whether or not a thread can be considered new or unread for
    | any logged in user. Increasing this value to cover a longer period will
    | increase the ultimate size of your forum_threads_read table. Must be a
    | valid strtotime() string, or set to false to completely disable
    | age-sensitive thread features.
    |
    */

    'old_thread_threshold' => '-1 month',

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
        'post|put' => [
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
        'patch' => [
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
    | Cache lifetime
    |--------------------------------------------------------------------------
    |
    | Cache lifetime, in minutes.
    |
    */

    'cache_lifetime' => 5,

    /*
    |--------------------------------------------------------------------------
    | Soft deletes
    |--------------------------------------------------------------------------
    |
    | Disable this if you want categories, threads and posts to be permanently
    | removed from your database when they're deleted. Note that by default,
    | the option exists to force delete models regardless of this setting.
    |
    */

    'soft_deletes' => true,

    /*
    |--------------------------------------------------------------------------
    | List soft-deleted threads/posts
    |--------------------------------------------------------------------------
    |
    | Enable these if you want to include soft-deleted threads and posts in
    | categories and threads (respectively). Note that this does not affect
    | the ability to view a soft-deleted thread or post directly, which
    | is determined by the forum policies.
    |
    */

    'list_trashed_threads' => false,
    'list_trashed_posts' => false

];
