<?php namespace Riari\Forum\Contracts\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface ReceiverContract
{
    /**
     * Handle a response from the dispatcher for the given request.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return Response|mixed
     */
    public function handleResponse(Request $request, Response $response);
}
