<?php

/**
 * Process an alert message to display to the user.
 *
 * @param  string  $type
 * @param  string  $message
 * @return void
 */
function alert($type, $message)
{
    $processAlert = config('forum.integration.process_alert');
    $processAlert($type, $message);
}

/**
 * Determine if the specified user is permitted to access a named route.
 *
 * @param  array  $parameters
 * @param  string  $routeName
 * @param  object  $user
 * @return boolean
 */
function permitted($parameters, $routeName, $user)
{
    $action = config("forum.permissions.{$routeName}");

    if (is_callable($action)) {
        return $action($parameters, $user);
    }

    return false;
}
