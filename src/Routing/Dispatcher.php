<?php

namespace Riari\Forum\Routing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class Dispatcher
{
    /**
     * @var Request
     */
    protected $currentRequest;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Create a new dispatcher instance.
     *
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->currentRequest = $request;
    }

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
        $this->uri = $uri;
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
        $this->parameters = $parameters;
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
        $request = Request::create($this->uri, $verb, $this->parameters);

        // Replace the request input for the duration of the dispatched request
        $input = $this->currentRequest->input();
        $this->currentRequest->replace($request->input());
        $response = Route::dispatch($request);
        $this->currentRequest->replace($input);

        return $response->getOriginalContent();
    }
}
