<?php namespace Riari\Forum\Libraries;

use App;

class AccessControl
{
    /**
     * Determine if the specified user has access to a named route.
     *
     * @param  object  $route
     * @param  object  $user
     */
    public static function check($route, $user, $abort = true)
    {
        // Check for access permission
        $accessCallback = config('forum.permissions.access_category');
        $granted = $accessCallback($route->parameters(), $user);

        if ($granted && ($permission != 'access_category'))
        {
            // Check for action permission
            $actionCallback = config('forum.permissions.' . $route->action()['as']);
            $granted = $actionCallback($route->parameters(), $user);
        }

        if (!$granted && $abort)
        {
            $deniedCallback = config('forum.integration.process_denied');
            $deniedCallback($route->parameters(), $user);
        }

        return $granted;
    }

}
