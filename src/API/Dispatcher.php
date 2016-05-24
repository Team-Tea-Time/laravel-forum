<?php namespace Riari\Forum\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Riari\Forum\Contracts\API\ReceiverContract;

class Dispatcher
{
    /**
     * @var ReceiverContract
     */
    protected $receiver;

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
     * @param  ReceiverContract  $receiver
     */
    public function __construct(ReceiverContract $receiver)
    {
        $this->receiver = $receiver;
        $this->currentRequest = request();
    }

    /**
     * Set the request URI via a named route, optionally with route parameters.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return Dispatcher
     */
    public function route($name, $parameters = [])
    {
        return $this->uri(route($name, $parameters, false));
    }

    /**
     * Set the request URI.
     *
     * @param  string  $uri
     * @return Dispatcher
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
     * @return Dispatcher
     */
    public function parameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Dispatch a GET request.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->dispatch();
    }

    /**
     * Dispatch a POST request.
     *
     * @return mixed
     */
    public function post()
    {
        return $this->dispatch('POST');
    }

    /**
     * Dispatch a PATCH request.
     *
     * @return mixed
     */
    public function patch()
    {
        return $this->dispatch('PATCH');
    }

    /**
     * Dispatch a DELETE request.
     *
     * @return mixed
     */
    public function delete()
    {
        return $this->dispatch('DELETE');
    }

    /**
     * Dispatch a request.
     *
     * @param  string  $verb
     * @return mixed
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

        return $this->receiver->handleResponse($request, $response);
    }
}
