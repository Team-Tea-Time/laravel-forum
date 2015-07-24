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
    | Cache settings
    |--------------------------------------------------------------------------
    |
    | Duration to cache data such as thread and post counts (in minutes).
    |
    */

    'cache' => [
        'lifetime'  => 5
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation settings
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'category' => [
            'category_id'   => 'integer|exists:forum_categories,id',
            'title'         => 'required',
            'weight'        => 'integer'
        ],
        'thread' => [
            'title'         => 'required'
        ],
        'post' => [
            'content'       => 'required|min:5'
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
