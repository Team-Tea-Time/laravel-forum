<?php namespace Riari\Forum\Libraries;

use App;
use Config;

class AccessControl {

    public static function check($context, $permission, $abort = true)
    {
        // Fetch the current user
        $user_callback = Config::get('forum::integration.current_user');
        $user = $user_callback();

        // Check for access permission
        $access_callback = Config::get('forum::permissions.access_category');
        $permission_granted = $access_callback($context, $user);

        if ($permission_granted && ($permission != 'access_category'))
        {
            // Check for action permission
            $action_callback = Config::get('forum::permissions.' . $permission);
            $permission_granted = $action_callback($context, $user);
        }

        if (!$permission_granted && $abort)
        {
            App::abort(403, 'Access denied.');
        }

        return $permission_granted;
    }

}
