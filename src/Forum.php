<?php

namespace Riari\Forum;

use App;
use ForumRoute;

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
     * Render the given content.
     *
     * @param  string  $content
     * @return string
     */
    public static function render($content)
    {
        return nl2br(e($content));
    }

    /**
     * Helper function for binding route parameters.
     *
     * @param  mixed  $model
     * @param  int  $id
     * @return mixed
     */
    public static function bindParameter($model, $id)
    {
        if (ForumRoute::isAPI()) {
            return $model->withTrashed()->find($id);
        }
        return $model->findOrFail($id);
    }
}
