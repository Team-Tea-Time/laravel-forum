<?php namespace Eorzea\Forum;

use Config;

class AccessControl {

  public static function check( $context, $permission )
  {
    // Fetch the current user
    $user_callback = Config::get('forum::integration.current_user');
    $user = $user_callback();

    // Check for access permission
    $access_callback = Config::get('forum::permissions.access_forum');
    $permission_granted = $access_callback($context, $user);

    if ( $permission_granted && ( $permission != 'access_forums' ) )
    {
      // Check for action permission
      $action_callback = Config::get('forum::permissions.' . $permission);
      $permission_granted = $action_callback($context, $user);
    }

    return $permission_granted;
  }

}
