<?php namespace Riari\Forum;

use App;

class Forum
{
    /**
     * Process an alert message to display to the user.
     *
     * @param  string  $type
     * @param  string  $message
     * @return void
     */
    public static function alert($type, $message)
    {
        $processAlert = config('forum.integration.process_alert');
        $processAlert($type, $message);
    }

    /**
     * Determine if a permission is granted for a user.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @param  object  $user
     * @return boolean
     */
    public static function permitted($name, $parameters, $user)
    {
        // Strip 'forum.' from the beginning of the permission name
        $name = str_replace('forum.', '', $name);

        // Fetch permission aliases
        $aliases = config('forum.permissions.aliases');

        // If this permission has an alias, use it
        if (isset($aliases[$name])) {
            $name = $aliases[$name];
        }

        // Determine which handling method to use
        switch (config('forum.permissions.method')) {
            case 'class':
                // We're using a class; get the class name, make sure it's valid
                // and return the result of handle() if possible
                $class = config('forum.permissions.class');

                if (class_exists($class)) {
                    $handler = App::make($class);

                    if (method_exists($handler, 'handle')) {
                        return $handler->handle($name, $parameters, $user);
                    }
                }
                break;
            case 'callback':
            default:
                // We're using callbacks; get the callback, make sure it's valid
                // and return its result if possible
                $permitted = config("forum.permissions.callback.{$name}");

                if (is_callable($permitted)) {
                    return $permitted($parameters, $user);
                }
        }

        // Default to returning false in case of bad config
        return false;
    }

    /**
     * Determine if a permission is granted for the current user.
     *
     * @param  mixed  $permission
     * @param  array  $parameters
     * @param  boolean  $all
     * @return boolean
     */
    public static function userCan($permission, $parameters = [], $all = false)
    {
        // Just return true if the built-in permissions are disabled.
        if (!config('forum.permissions.enabled')) {
            return true;
        }

        $user = auth()->user();

        // Are we checking for multiple permissions, or just one?
        if (is_array($permission)) {
            // Set the return value for the loop. We base this on $all because
            // if we're checking to see if all permission checks pass (i.e.
            // $all == true), we need to return false on the first denied
            // permission, otherwise (if $all == false) we need to return true
            // on the first granted permission.
            $return = !$all;

            foreach ($permission as $p) {
                // Check the permission and return as appropriate
                if ($all !== self::permitted($p, $parameters, $user)) {
                    return $return;
                }
            }

            // Loop completed without returning; if $all == true, that means
            // all permission checks passed, otherwise none of them did, so just
            // return $all
            return $all;
        }

        return self::permitted($permission, $parameters, $user);
    }
}
