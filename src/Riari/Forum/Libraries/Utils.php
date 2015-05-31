<?php namespace Riari\Forum\Libraries;

class Utils {

    /**
     * Get the current user object (if any) via the integration.current_user
     * closure.
     */
    public static function getCurrentUser()
    {
        $current_user_callback = config('forum.integration.current_user');
        return $current_user_callback();
    }

}
