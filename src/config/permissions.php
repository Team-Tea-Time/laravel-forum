<?php

return array(

  /*
  |--------------------------------------------------------------------------
  | ACCESS: Category permissions
  |--------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to access a given
  | category. All categories are open by default.
  |
  */
  'access_category' => function($category, $user)
  {
    return TRUE;
  },

  /*
  |--------------------------------------------------------------------------
  | ACTION: New thread permission
  |--------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to post new
  | threads.
  |
  */
  'create_threads' => function($category, $user)
  {
    if ($user == NULL)
    {
      return FALSE;
    }

    return TRUE;
  },

  /*
  |--------------------------------------------------------------------------
  | ACTION: Delete thread permission
  |--------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to delete threads.
  |
  */
  'delete_threads' => function($thread, $user)
  {
    return FALSE;
  },

  /*
  |--------------------------------------------------------------------------
  | ACTION: Reply to thread permission
  |--------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to post thread
  | replies.
  |
  */
  'reply_to_thread' => function($thread, $user)
  {
    if ($user == NULL)
    {
      return FALSE;
    }

    return TRUE;
  },

  /*
  |--------------------------------------------------------------------------
  | ACTION: Edit post permission
  |--------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to edit a given
  | post.
  |
  */
  'edit_post' => function($post, $user)
  {
    if ($user == NULL || ($user->id != $post->author_id))
    {
      return FALSE;
    }

    return TRUE;
  },

  /*
  |--------------------------------------------------------------------------
  | ACTION: Delete post permission
  |--------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to delete posts.
  |
  */
  'delete_posts' => function($post, $user)
  {
    return FALSE;
  }

);
