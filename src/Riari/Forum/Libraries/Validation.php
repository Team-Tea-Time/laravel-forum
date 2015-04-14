<?php namespace Riari\Forum\Libraries;

use Config;
use Input;
use Riari\Forum\Libraries\Alerts;
use Validator;

class Validation {

    public static function processValidationMessages($messages)
    {
        foreach ($messages as $message)
        {
            Alerts::add('danger', $message);
        }
    }

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
