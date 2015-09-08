<?php

namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Riari\Forum\Forum;

abstract class BaseController extends Controller
{
    use ValidatesRequests;

    /**
     * @var mixed
     */
    protected $model;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var string
     */
    protected $translationFile;

    /**
     * GET: return an index of models.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return $this->collectionResponse($this->model->paginate());
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

        $model = $this->model->create($request->all());

        return $this->modelResponse($model, $this->trans('created'), 201);
    }

    /**
     * GET: return a model by ID.
     *
     * @param  mixed  $model
     * @return JsonResponse
     */
    public function show($model = null)
    {
        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
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

        return $this->modelResponse($response, $this->trans('updated'));
    }

    /**
     * DELETE: delete a model.
     *
     * @param  Model  $model
     * @param  Request  $request
     * @return JsonResponse
     */
    public function destroy($model, Request $request)
    {
        if (!$model->exists) {
            return $this->notFoundResponse();
        }

        if ($request->has('force') && $request->input('force') == 1) {
            $model->forceDelete();
            $message = $this->trans('perma_deleted');
        } elseif (!$model->trashed()) {
            $model->delete();
            $message = $this->trans('deleted');
        }

        return $this->modelResponse($model, $message);
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

        if ($model->trashed()) {
            $model->restore();
        }

        return $this->modelResponse($model, $this->trans('restored'));
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
            $model = $this->model->destroy($id);

            if (!is_null($model)) {
                $collection->push($model);
            }
        }

        return $this->collectionResponse($collection, $this->trans('deleted', $collection->count()));
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
            $model = $this->model->restore($id);

            if (!is_null($model)) {
                $collection->push($model);
            }
        }

        return $this->collectionResponse($collection, $this->trans('restored', $collection->count()));
    }

    /**
     * Bulk update an attribute.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    protected function bulkUpdate(Request $request)
    {
        $this->validate($request, ['id' => 'required']);

        $input = $request->all();
        $request->replace($request->except('id'));
        $collection = collect();
        foreach ($input['id'] as $id) {
            $model = $this->model->find($id);

            if (!is_null($model) && $model->exists) {
                $collection->push($this->doUpdate($model, $request));
            }
        }

        return $this->collectionResponse($collection, $this->trans('updated', $collection->count()));
    }

    /**
     * Update a model.
     *
     * @param  Model  $model
     * @param  Request  $request
     * @return mixed
     */
    protected function doUpdate($model, Request $request)
    {
        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
        }

        $this->validate($request, $this->rules['update']);

        $model->update($request->all());

        return $model;
    }

    /**
     * Create a generic response.
     *
     * @param  object  $data
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    protected function response($data, $message = "", $code = 200)
    {
        $message = (empty($message)) ? [] : ['message' => $message];
        return new JsonResponse($message + compact('data'), $code);
    }

    /**
     * Create a Collection response.
     *
     * @param  object  $collection
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    protected function collectionResponse($collection, $message = "", $code = 200)
    {
        return $this->response($collection, $message, $code);
    }

    /**
     * Create a Model response.
     *
     * @param  object  $model
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    protected function modelResponse($model, $message = "", $code = 200)
    {
        return $this->response($model, $message, $code);
    }

    /**
     * Create a 'not found' response.
     *
     * @return JsonResponse
     */
    protected function notFoundResponse()
    {
        return new JsonResponse(['error' => "The requested URL is invalid."], 404);
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
            'validation_errors' => $errors
        ], 422);
    }

    /**
     * Fetch a translated string.
     *
     * @param  string  $key
     * @param  int  $count
     * @return string
     */
    protected function trans($key, $count = 1)
    {
        return trans_choice("forum::{$this->translationFile}.{$key}", $count);
    }
}
