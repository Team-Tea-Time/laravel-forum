<?php namespace Riari\Forum\Libraries;

use App;
use Config;
use Riari\Forum\Libraries\Utils;

class AccessControl {

    /**
     * Check a named permission against the given content (category, thread or
     * post) using the permission closures in permissions.
     */
    public static function check($context, $permission, $abort = true)
    {
        $user = Utils::getCurrentUser();

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
            $denied_callback = Config::get('forum::integration.process_denied');
            $denied_callback($context, $user);
        }

        return $permission_granted;
    }

}
