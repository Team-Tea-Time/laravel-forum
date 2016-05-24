<?php namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $rules;

    /**
     * Create a new API controller instance.
     *
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->validate($request, [
            'with'      => 'array',
            'append'    => 'array',
            'orderBy'   => 'string',
            'orderDir'  => 'in:desc,asc'
        ]);
    }

    /**
     * Return the model to use for this controller.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    abstract protected function model();

    /**
     * Return the translation file name to use for this controller.
     *
     * @return string
     */
    abstract protected function translationFile();

    /**
     * PATCH: Update a model.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function update($id, Request $request)
    {
        return $this->updateModel($this->model()->find($id), $request->all(), 'edit');
    }

    /**
     * DELETE: Delete a model.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function destroy($id, Request $request)
    {
        $model = $this->model();

        $force = false;
        if (method_exists($model, 'forceDelete')) {
            $this->validate($request, ['force' => ['boolean']]);

            $model = $model->withTrashed();
            $force = (bool) $request->input('force');
        }

        return $this->deleteModel($model->find($id), 'delete', $force);
    }

    /**
     * PATCH: Restore a model.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function restore($id, Request $request)
    {
        $model = $this->model()->withTrashed()->find($id);

        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
        }

        if ($model->trashed()) {
            $model->timestamps = false;
            $model->restore();
            $model->timestamps = true;

            return $this->response($model, $this->trans('restored'));
        }

        return $this->notFoundResponse();
    }

    /**
     * DELETE: Delete models in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkDestroy(Request $request)
    {
        return $this->bulk($request, 'destroy', 'updated', $request->only('force'));
    }

    /**
     * PATCH: Restore models in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkRestore(Request $request)
    {
        return $this->bulk($request, 'restore', 'updated');
    }

    /**
     * Carry out a bulk action.
     *
     * @param  Request  $request
     * @param  string  $action
     * @param  string  $transKey
     * @param  array  $input
     * @return JsonResponse|Response
     */
    protected function bulk(Request $request, $action, $transKey, array $input = [])
    {
        $this->validate($request, ['items' => 'required']);

        $items = $request->input('items');
        $request->replace($input);
        $models = collect();

        foreach ($items as $id) {
            $response = $this->{$action}($id, $request);

            if (!$response->isNotFound()) {
                if ($response->isClientError()) {
                    return $response;
                }

                $models->push($response->getOriginalContent());
            }
        }

        return $this->response($models, $this->trans($transKey, $models->count()));
    }

    /**
     * Update a given model's attributes.
     *
     * @param  Model  $model
     * @param  array  $attributes
     * @param  array|string  $authorize
     * @return JsonResponse|Response
     */
    protected function updateModel($model, array $attributes, $authorize = [])
    {
        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
        }

        $this->parseAuthorization($model, $authorize);

        $model->update($attributes);

        return $this->response($model, $this->trans('updated'));
    }

    /**
     * Delete a model.
     *
     * @param  Model  $model
     * @param  array|string  $authorize
     * @param  bool  $force
     * @return JsonResponse|Response
     */
    protected function deleteModel($model, $authorize = [], $force = false)
    {
        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
        }

        $this->parseAuthorization($model, $authorize);

        if ($force) {
            $model->forceDelete();

            return $this->response($model, $this->trans('perma_deleted'));
        } else {
            $model->timestamps = false;
            $model->delete();
            $model->timestamps = true;

            return $this->response($model, $this->trans('deleted'));
        }
    }

    /**
     * Parse an authorization parameter and authorize if applicable.
     *
     * @param  Model  $model
     * @param  array|string  $authorize
     * @return JsonResponse|Response
     */
    protected function parseAuthorization($model, $authorize = [])
    {
        if (!empty($authorize)) {
            // We need to authorize this change

            if (is_string($authorize)) {
                // Only an ability name was given, so use $model
                $authorize = [$authorize, $model];
            }

            list($ability, $authorizeModel) = $authorize;

            $this->authorize($ability, $authorizeModel);
        }
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return void
     *
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    public function validate(Request $request, array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $rules = array_merge_recursive(config('forum.validation.rules'), $rules);

        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }
    }

    /**
     * Create a generic response.
     *
     * @param  object  $data
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse|Response
     */
    protected function response($data, $message = "", $code = 200)
    {
        $message = empty($message) ? [] : compact('message');

        return (request()->ajax() || request()->wantsJson())
            ? new JsonResponse($message + compact('data'), $code)
            : new Response($data, $code);
    }

    /**
     * Create a 'not found' response.
     *
     * @return JsonResponse|Response
     */
    protected function notFoundResponse()
    {
        $content = ['error' => "Resource not found."];

        return (request()->ajax() || request()->wantsJson())
            ? new JsonResponse($content, 404)
            : new Response($content, 404);
    }

    /**
     * Create the response for when a request fails validation.
     *
     * @param  Request  $request
     * @param  array|string  $errors
     * @return JsonResponse|Response
     */
    protected function buildFailedValidationResponse(Request $request, $errors)
    {
        $content = [
            'error'             => "The submitted data did not pass validation.",
            'validation_errors' => (array) $errors
        ];

        return ($request->ajax() && !$request->pjax() || $request->wantsJson())
            ? new JsonResponse($content, 422)
            : new Response($content, 422);
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
        $file = $this->translationFile();
        return trans_choice("forum::{$file}.{$key}", $count);
    }
}
