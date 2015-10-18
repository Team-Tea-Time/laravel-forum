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
