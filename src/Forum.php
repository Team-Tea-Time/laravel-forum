<?php

namespace Riari\Forum;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class Forum
{
    /**
     * Process an alert message to display to the user.
     *
     * @param  string  $type
     * @param  string  $transFile
     * @param  string  $transKey
     * @param  string  $transCount
     * @return void
     */
    public static function alert($type, $transFile, $transKey, $transCount = 1, $transParameters = [])
    {
        $processAlert = config('forum.integration.process_alert');
        $processAlert($type, self::trans($transFile, $transKey, $transCount, $transParameters));
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
     * Translation wrapper.
     *
     * @param  string  $file
     * @param  string  $key
     * @param  int  $count
     * @return string
     */
    public static function trans($file, $key, $count = 1, $parameters = [])
    {
        return trans_choice("forum::{$file}.{$key}", $count, $parameters);
    }
}
