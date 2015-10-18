<?php

namespace Riari\Forum\API;

use Illuminate\Http\Request;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Support\Facades\Route;

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
     * @var array
     */
    protected $headers = [];

    /**
     * Create a new dispatcher instance.
     */
    public function __construct()
    {
        $this->currentRequest = request();
    }

    /**
     * Set the request URI via a named route, optionally with route parameters.
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
     * @param  array  $headers
     * @return Response
     */
    public function dispatch($verb = 'GET')
    {
        $request = Request::create($this->uri, $verb, $this->parameters);

        // Set token authorization header
        $request->headers->set('Authorization', 'Token token="' . config('forum.api.token') . '"');

        // Replace the request input for the duration of the dispatched request
        $input = $this->currentRequest->input();
        $this->currentRequest->replace($request->input());
        $response = Route::dispatch($request);
        $this->currentRequest->replace($input);

        return $response->getOriginalContent();
    }
}
