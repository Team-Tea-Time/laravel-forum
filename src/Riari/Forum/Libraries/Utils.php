<?php namespace Riari\Forum\Libraries;

use Config;

class Utils {

    /**
     * Get the current user object (if any) via the integration.current_user
     * closure.
     */
    public static function getCurrentUser()
    {
        $current_user_callback = Config::get('forum::integration.current_user');
        return $current_user_callback();
    }

}
