<?php

return array(

  /*
  |-----------------------------------------------------------------------------
  | New thread permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to post new threads.
  |
  */
  'create_threads' => function() {},

  /*
  |-----------------------------------------------------------------------------
  | Delete thread permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to delete threads.
  |
  */
  'delete_threads' => function() {},

  /*
  |-----------------------------------------------------------------------------
  | New post/reply permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to post thread replies.
  |
  */
  'create_posts' => function() {},


  /*
  |-----------------------------------------------------------------------------
  | Edit post permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to edit a given post.
  |
  */
  'update_post' => function() {},


  /*
  |-----------------------------------------------------------------------------
  | Delete post permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to delete posts.
  |
  */
  'delete_posts' => function() {},


  /*
  |-----------------------------------------------------------------------------
  | Forum permissions
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to access a given
  | forum.
  |
  */
  'access_forum' => function()
  {
    $forum_roles = array();
  }

);
