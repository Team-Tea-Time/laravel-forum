<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Riari\Forum\API\Dispatcher;

abstract class BaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a frontend controller instance.
     *
     * @param  Dispatcher  $dispatcher
     */
    public function __construct()
    {
        $this->dispatcher = new Dispatcher;
    }

    /**
     * Return an API dispatcher instance.
     *
     * @param  string  $route
     * @param  array  $parameters
     * @return Dispatcher
     */
    public function api($route, $parameters = [])
    {
        return $this->dispatcher->route("forum.api.{$route}", $parameters);
    }
}
