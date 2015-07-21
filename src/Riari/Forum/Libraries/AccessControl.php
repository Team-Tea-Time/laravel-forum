<?php namespace Riari\Forum\Libraries;

use App;

class AccessControl
{
    /**
     * Determine if the specified user has access to a named route.
     *
     * @param  array  $parameters
     * @param  string  $routeName
     * @param  object  $user
     */
    public static function check($parameters, $routeName, $user, $abort = true)
    {
        // Check for access permission
        $accessCallback = config('forum.permissions.access_category');
        $granted = $accessCallback($parameters, $user);

        if ($granted && ($routeName != 'forum.category.index'))
        {
            // Check for action permission
            $actionCallback = config('forum.permissions.' . $routeName);
            $granted = $actionCallback($parameters, $user);
        }

        if (!$granted && $abort)
        {
            $deniedCallback = config('forum.integration.process_denied');
            $deniedCallback($parameters, $user);
        }

        return $granted;
    }

}
