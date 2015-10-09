<?php

namespace Riari\Forum\Routing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class Dispatcher
{
    /**
     * @var string
     */
    protected $requestURI;

    /**
     * @var array
     */
    protected $requestParameters = [];

    /**
     * Set the request URI via a named route.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return this
     */
    public function route($name, $parameters = [])
    {
        return $this->uri(route($name, $parameters, false));
    }

    /**
     * Set the request URI.
     *
     * @param  string  $uri
     * @return this
     */
    public function uri($uri)
    {
        $this->requestURI = $uri;
        return $this;
    }

    /**
     * Set the request parameters.
     *
     * @param  array  $parameters
     * @return this
     */
    public function parameters($parameters)
    {
        $this->requestParameters = $parameters;
        return $this;
    }

    /**
     * Dispatch a GET request.
     *
     * @return Response
     */
    public function get()
    {
        return $this->dispatch();
    }

    /**
     * Dispatch a POST request.
     *
     * @return Response
     */
    public function post()
    {
        return $this->dispatch('POST');
    }

    /**
     * Dispatch a PUT request.
     *
     * @return Response
     */
    public function put()
    {
        return $this->dispatch('PUT');
    }

    /**
     * Dispatch a PATCH request.
     *
     * @return Response
     */
    public function patch()
    {
        return $this->dispatch('PATCH');
    }

    /**
     * Dispatch a DELETE request.
     *
     * @return Response
     */
    public function delete()
    {
        return $this->dispatch('DELETE');
    }

    /**
     * Dispatch a request.
     *
     * @return Response
     */
    public function dispatch($verb = 'GET')
    {
        $request = Request::create($this->requestURI, $verb, $this->requestParameters);
        return Route::dispatch($request);
    }
}
