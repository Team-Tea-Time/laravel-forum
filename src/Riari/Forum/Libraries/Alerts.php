<?php namespace Riari\Forum\Libraries;

class Alerts
{
    public static function add($type, $message)
    {
        $process_alert_callback = config('forum.integration.process_alert');
        $process_alert_callback($type, $message);
    }
}
