<?php namespace Riari\Forum\Libraries;

use Config;

class Alerts {

    public static function add($type, $message)
    {
        $process_alert_callback = Config::get('forum::integration.process_alert');

        $process_alert_callback($type, $message);
    }

}
