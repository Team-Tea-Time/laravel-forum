<?php namespace Riari\Forum\Libraries;

use Config;
use Input;
use Riari\Forum\Libraries\Alerts;
use Validator;

class Validation {

    /**
     * Add an alert for each of the given messages.
     */
    public static function processValidationMessages($messages = array())
    {
        foreach ($messages as $message)
        {
            Alerts::add('danger', $message);
        }
    }

    /**
     * Validate the current input using the specified ruleset, as defined in
     * the preferences.validation_rules config array.
     */
    public static function check($type = 'thread')
    {
        $rules = Config::get('forum::preferences.validation_rules');
        $validator = Validator::make(Input::all(), $rules[$type]);

        if (!$validator->passes())
        {
            self::processValidationMessages($validator->messages()->all());
            return false;
        }

        return true;
    }

}
