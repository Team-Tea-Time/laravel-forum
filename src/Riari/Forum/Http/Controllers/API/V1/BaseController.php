<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Riari\Forum\Http\Config\API\Error;

abstract class BaseController extends Controller
{
    use ValidatesRequests;

    /**
     * @var mixed
     */
    protected $repository;

	/**
	 * @var array
	 */
	protected $rules;

    /**
     * GET: return an index of models.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return $this->collectionResponse($this->repository->paginate());
    }

    /**
     * POST: create a new model.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $v = $this->validate($request, $this->rules['store']);

        if ($v instanceof JsonResponse) {
            return $v;
        }

        $model = $this->repository->create($request->all());

        return $this->modelResponse($model, 201);
    }

    /**
     * GET: return a model by ID.
     *
     * @param  Model  $model
     * @return JsonResponse
     */
    public function show($model)
    {
        if (!$model->exists) {
            return $model->notFoundResponse();
        }

        return $this->modelResponse($model);
    }

    /**
     * PUT/PATCH: update a model.
     *
     * @param  Model  $model
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update($model, Request $request)
    {
        if (!$model->exists) {
            return $this->notFoundResponse();
        }

        $v = $this->validate($request, $this->rules['update']);

        if ($v instanceof JsonResponse) {
            return $v;
        }

        $model = $this->repository->update(
            $model->id,
            $request->all()
        );

        return $this->modelResponse($model);
    }

    /**
     * DELETE: delete a model.
     *
     * @param  Model  $model
     * @param  int  $code
     * @return JsonResponse
     */
    public function destroy($model)
    {
        if (!$model->exists) {
            return $this->notFoundResponse();
        }

        $this->repository->delete($model->id);

        return $this->modelResponse($model);
    }

    /**
     * Create a Collection response.
     *
     * @param  object  $collection
     * @param  int  $code
     * @return JsonResponse
     */
    protected function collectionResponse($collection, $code = 200)
    {
        return new JsonResponse($collection, $code);
    }

    /**
     * Create a Model response.
     *
     * @param  object  $model
     * @param  int  $code
     * @return JsonResponse
     */
    protected function modelResponse($model, $code = 200)
    {
        return new JsonResponse(['data' => $model], $code);
    }

    /**
     * Create a 'not found' response.
     *
     * @return JsonResponse
     */
    protected function notFoundResponse()
    {
        return new JsonResponse([
            'error'     => "The requested URL is invalid.",
            'code'      => Error::NOT_FOUND
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
