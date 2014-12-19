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
  'create_threads' => function( $context, $user )
  {
    if( $user == NULL )
    {
      return FALSE;
    }

    return TRUE;
  },

  /*
  |-----------------------------------------------------------------------------
  | Delete thread permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to delete threads.
  |
  */
  'delete_threads' => function( $context, $user )
  {
    return FALSE;
  },

  /*
  |-----------------------------------------------------------------------------
  | New post/reply permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to post thread replies.
  |
  */
  'create_posts' => function( $context, $user )
  {
    if( $user == NULL )
    {
      return FALSE;
    }

    return TRUE;
  },


  /*
  |-----------------------------------------------------------------------------
  | Edit post permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to edit a given post.
  |
  */
  'update_post' => function( $context, $user )
  {
    if( $user == NULL )
    {
      return FALSE;
    }

    return TRUE;
  },


  /*
  |-----------------------------------------------------------------------------
  | Delete post permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to delete posts.
  |
  */
  'delete_posts' => function( $context, $user )
  {
    return FALSE;
  },


  /*
  |-----------------------------------------------------------------------------
  | Forum permissions
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to access a given
  | forum. All forums are open by default.
  |
  */
  'access_forum' => function( $context, $user )
  {
    return TRUE;
  }

);
