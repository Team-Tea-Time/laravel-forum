<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Http\Config\API\Error;
use Riari\Forum\Http\Controllers\BaseController as Controller;

class BaseController extends Controller
{
    /**
     * Create a Collection response.
     *
     * @param  object  $collection
     */
    protected function collectionResponse($collection)
    {
        return new JsonResponse($collection);
    }

    /**
     * Create a Model response.
     *
     * @param  object  $model
     */
    protected function modelResponse($model)
    {
        return new JsonResponse(['data' => $model]);
    }

    /**
     * Create a 'not found' response.
     */
    protected function notFoundResponse()
    {
        return new JsonResponse([
            'error' => "The requested URL is invalid.",
            'code'  => Error::NOT_FOUND
        ], 404);
    }

    /**
     * Create the response for when a request fails validation.
     *
     * @param  Request  $request
     * @param  array  $errors
     * @return JsonResponse
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        return new JsonResponse([
            'error'             => "The submitted data did not pass validation.",
            'code'              => Error::VALIDATION_FAILED,
            'validation_errors' => $errors
        ], 422);
    }
}
