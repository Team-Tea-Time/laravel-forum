<?php

namespace Riari\Forum\Routing;

class Route extends \Illuminate\Support\Facades\Route
{
    public static function isAPI()
    {
        $action = self::current()->getAction();
        return (isset($action['name']) && $action['name'] == 'api');
    }
}
