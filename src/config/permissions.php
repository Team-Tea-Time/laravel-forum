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
  | New post/reply permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to post thread replies.
  |
  */
  'post_replies' => function() {},


  /*
  |-----------------------------------------------------------------------------
  | Edit post permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to edit a given post.
  |
  */
  'edit_posts' => function() {},


  /*
  |-----------------------------------------------------------------------------
  | Forum permissions
  |-----------------------------------------------------------------------------
  |
  | Specifies role-restricted forums and the roles needed to access them.
  |
  */
  'access_forums' => array()

);
