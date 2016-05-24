<?php namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Riari\Forum\Models\Category;

class CategoryController extends BaseController
{
    /**
     * Return the model to use for this controller.
     *
     * @return Category
     */
    protected function model()
    {
        return new Category;
    }

    /**
     * Return the translation file name to use for this controller.
     *
     * @return string
     */
    protected function translationFile()
    {
        return 'categories';
    }

    /**
     * GET: Return an index of categories.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function index(Request $request)
    {
        $categories = $this->model()->withRequestScopes($request);

        $categories = $categories->get()->filter(function ($category) {
            if ($category->private) {
                return Gate::allows('view', $category);
            }

            return true;
        });

        return $this->response($categories);
    }

    /**
     * GET: Return a category by ID.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function fetch($id, Request $request)
    {
        $category = $this->model()->find($id);

        if (is_null($category) || !$category->exists) {
            return $this->notFoundResponse();
        }

        if ($category->private) {
            $this->authorize('view', $category);
        }

        return $this->response($category);
    }

    /**
     * POST: Create a new category.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $this->authorize('createCategories');

        $this->validate($request, [
            'title'             => ['required'],
            'weight'            => ['required'],
            'enable_threads'    => ['required'],
            'private'           => ['required']
        ]);

        $category = $this->model()->create($request->only(['category_id', 'title', 'description', 'weight', 'enable_threads', 'private']));

        return $this->response($category, 201);
    }

    /**
     * DELETE: Delete a category.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function destroy($id, Request $request)
    {
        $category = $this->model()->find($id);

        if (!$category->threads->isEmpty() || !$category->children->isEmpty()) {
            return $this->buildFailedValidationResponse($request, trans('forum::validation.category_is_empty'));
        }

        return $this->deleteModel($category, 'delete');
    }

    /**
     * PATCH: Move a category.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function move($id, Request $request)
    {
        $this->authorize('moveCategories');
        $this->validate($request, ['category_id' => ['required']]);

        $category = $this->model()->find($id);

        return $this->updateModel($category, ['category_id' => $request->input('category_id')]);
    }

    /**
     * PATCH: Enable threads in a category.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function enableThreads($id, Request $request)
    {
        $category = $this->model()->where('enable_threads', 0)->find($id);

        return $this->updateModel($category, ['enable_threads' => 1], 'enableThreads');
    }

    /**
     * PATCH: Disable threads in a category.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function disableThreads($id, Request $request)
    {
        $category = $this->model()->where('enable_threads', 1)->find($id);

        if (!$category->threads->isEmpty()) {
            return $this->buildFailedValidationResponse($request, trans('forum::validation.category_has_no_threads'));
        }

        return $this->updateModel($category, ['enable_threads' => 0], 'enableThreads');
    }

    /**
     * PATCH: Make a category public.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function makePublic($id, Request $request)
    {
        $this->authorize('createCategories');

        $category = $this->model()->where('private', 1)->find($id);

        return $this->updateModel($category, ['private' => 0]);
    }

    /**
     * PATCH: Make a category private.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function makePrivate($id, Request $request)
    {
        $this->authorize('createCategories');

        $category = $this->model()->where('private', 0)->find($id);

        return $this->updateModel($category, ['private' => 1]);
    }

    /**
     * PATCH: Rename a category.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function rename($id, Request $request)
    {
        $this->authorize('renameCategories');
        $this->validate($request, ['title' => ['required']]);

        $category = $this->model()->find($id);

        return $this->updateModel($category, $request->only(['title', 'description']));
    }

    /**
     * PATCH: Reorder a category.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function reorder($id, Request $request)
    {
        $this->authorize('moveCategories');
        $this->validate($request, ['weight' => ['required']]);

        $category = $this->model()->find($id);

        return $this->updateModel($category, ['weight' => $request->input('weight')]);
    }
}
