<?php namespace Riari\Forum;

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
     * Determine if the given user is permitted to access a named route.
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
}
