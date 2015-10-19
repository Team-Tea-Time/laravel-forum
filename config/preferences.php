<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Old thread threshold
    |--------------------------------------------------------------------------
    |
    | The minimum age of a thread before it should be considered old. This
    | determines whether or not a thread can be considered new or unread for
    | any user. Increasing this value to cover a longer period will increase
    | the ultimate size of your forum_threads_read table. Must be a valid
    | strtotime() string, or set to false to completely disable age-sensitive
    | thread features.
    |
    */

    'old_thread_threshold' => '7 days',

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'categories'    => 20, // Categories per page (API only)
        'threads'       => 20, // Threads per page
        'posts'         => 15  // Posts per page
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache lifetimes
    |--------------------------------------------------------------------------
    |
    | Here we specify cache lifetimes (in minutes) for various model data. Any
    | falsey values set here will cause the cache to use the default lifetime
    | for corresponding models/attributes.
    |
    */

    'cache_lifetimes' => [
        'default' => 5,
        'Category' => [
            'threadCount'   => 5,
            'postCount'     => 5,
            'deepestChild'  => 720,
            'depth'         => 720
        ]
    ],

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
