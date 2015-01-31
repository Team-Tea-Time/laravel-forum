<?php

return array(

  /*
  |--------------------------------------------------------------------------
  | Pagination settings
  |--------------------------------------------------------------------------
  */
  'threads_per_category' => 20,
  'posts_per_thread' => 15,
  'pagination_view' => 'pagination::simple',

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
  'validation_rules' => array(
    'thread' => [
      'title' => 'required'
    ],
    'post' => [
      'content' => 'required|min:5'
    ]
  ),

  /*
  |--------------------------------------------------------------------------
  | Misc settings
  |--------------------------------------------------------------------------
  */
  // Soft Delete: disable this if you want threads and posts to be permanently
  // removed from your database when they're deleted by a user.
  'soft_delete' => TRUE

);
