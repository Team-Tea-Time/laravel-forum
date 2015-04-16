<?php namespace Riari\Forum\Libraries;

use Config;

class Alerts {

    /**
     * Process an alert using the integration.process_alert closure.
     */
    public static function add($type, $message)
    {
        $process_alert_callback = Config::get('forum::integration.process_alert');

        $process_alert_callback($type, $message);
    }

}
