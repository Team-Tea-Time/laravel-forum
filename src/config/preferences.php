<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Thread settings
    |--------------------------------------------------------------------------
    */
    'thread' => [
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
    'threads_per_category' => 20,
    'posts_per_thread' => 15,

    /*
    |--------------------------------------------------------------------------
    | Cache settings
    |--------------------------------------------------------------------------
    |
    | Duration to cache data such as thread and post counts (in minutes).
    |
    */
    'cache_lifetime' => 5,

    /*
    |--------------------------------------------------------------------------
    | Validation settings
    |--------------------------------------------------------------------------
    */
    'validation_rules' => [
        'thread' => [
            'title' => 'required'
        ],
        'post' => [
            'content' => 'required|min:5'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Misc settings
    |--------------------------------------------------------------------------
    */
    // Soft Delete: disable this if you want threads and posts to be permanently
    // removed from your database when they're deleted by a user.
    'soft_delete' => true

];
