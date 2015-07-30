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
        $collection = $this->repository->paginate();

        return $this->collectionResponse();
    }

    /**
     * POST: create a new model.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules['store']);

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
        $response = $this->doUpdate($model, $request);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $this->modelResponse($response);
    }

    /**
     * DELETE: delete a model.
     *
     * @param  Model  $model
     * @return JsonResponse
     */
    public function destroy($model)
    {
        if (!$model->exists) {
            return $this->notFoundResponse();
        }

        $model = $this->repository->delete($model->id);

        return $this->modelResponse($model);
    }

    /**
     * PATCH: restore a model.
     *
     * @param  Model  $model
     * @return JsonResponse
     */
    public function restore($model)
    {
        if (!$model->exists) {
            return $this->notFoundResponse();
        }

        $this->repository->restore($model->id);

        return $this->modelResponse($model);
    }

    /**
     * DELETE: bulk delete models.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function bulkDestroy(Request $request)
    {
        $this->validate($request, ['id' => 'required']);

        $collection = collect();
        foreach ($request->input('id') as $id) {
            $model = $this->repository->delete($id);

            if (!is_null($model)) {
                $collection->push($model);
            }
        }

        return $this->collectionResponse($collection);
    }

    /**
     * PATCH: bulk restore models.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function bulkRestore(Request $request)
    {
        $this->validate($request, ['id' => 'required']);

        $collection = collect();
        foreach ($request->input('id') as $id) {
            $model = $this->repository->restore($id);

            if (!is_null($model)) {
                $collection->push($model);
            }
        }

        return $this->collectionResponse($collection);
    }

    /**
     * Bulk update an attribute.
     *
     * @param  Request  $request
     * @param  string  $attribute
     * @param  string  $rule
     * @return JsonResponse
     */
    protected function doBulkUpdate(Request $request, $attribute, $rule)
    {
        $this->validate($request, ['id' => 'required', $attribute => $rule]);

        $input = $request->all();
        $request->replace([
            $attribute => $input[$attribute]
        ]);
        $collection = collect();
        foreach ($input['id'] as $id) {
            $model = $this->repository->find($id);

            if (!is_null($model) && $model->exists) {
                $collection->push($this->doUpdate($model, $request));
            }
        }

        return $this->collectionResponse($collection);
    }

    /**
     * Update a model.
     *
     * @param  Model  $model
     * @param  Request  $request
     * @return Model
     */
    protected function doUpdate($model, Request $request)
    {
        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
        }

        $this->validate($request, $this->rules['update']);

        return $this->repository->update($model->id, $request->all());
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
