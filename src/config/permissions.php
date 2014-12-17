<?php

return array(

  /*
  |-----------------------------------------------------------------------------
  | New topic permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to post new topics.
  |
  */
  'postNewTopic' => function() {},

  /*
  |-----------------------------------------------------------------------------
  | New post/reply permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to post topic replies.
  |
  */
  'postNewMessage' => function() {},


  /*
  |-----------------------------------------------------------------------------
  | Edit post permission
  |-----------------------------------------------------------------------------
  |
  | Determines whether or not the current user is allowed to edit a given post.
  |
  */
  'postEditMessage' => function() {},


  /*
  |-----------------------------------------------------------------------------
  | Forum permissions
  |-----------------------------------------------------------------------------
  |
  | Specifies role-restricted forums and the roles needed to access them.
  |
  */
  'postInForums' => array()

);
