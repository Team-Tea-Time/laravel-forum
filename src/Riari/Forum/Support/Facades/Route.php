<?php namespace Riari\Forum\Support\Facades;

use Illuminate\Support\Facades\Route as BaseRoute;

class Route extends BaseRoute
{
    public static function isAPI()
    {
        $action = self::current()->getAction();
        return (isset($action['name']) && $action['name'] == 'api');
    }
}
