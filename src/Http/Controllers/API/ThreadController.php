<?php

namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Http\Requests\CreateThreadRequest;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class ThreadController extends BaseController
{
    /**
     * Return the model to use for this controller.
     *
     * @return Thread
     */
    protected function model()
    {
        return new Thread;
    }

    /**
     * Return the translation file name to use for this controller.
     *
     * @return string
     */
    protected function translationFile()
    {
        return 'threads';
    }

    /**
     * GET: return an index of threads by category ID.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function index(Request $request)
    {
        $this->validate($request, ['category_id' => 'required|integer|exists:forum_categories,id']);

        $threads = $this->model->where('category_id', $request->input('category_id'))->get();

        return $this->response($threads);
    }

    /**
     * GET: Return a thread by ID.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function fetch($id, Request $request)
    {
        $model = $this->model;

        if (
            $request->input('include_deleted') &&
            config('forum.preferences.list_trashed_posts') &&
            $request->user()->can('deletePosts', $model)
        ) {
            $model = $model->with('postsWithTrashed');
        }

        $model = $model->find($id);

        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
        }

        return $this->response($model);
    }

    /**
     * POST: Create a new thread.
     *
     * @param  CreateThreadRequest  $request
     * @return JsonResponse|Response
     */
    public function store(CreateThreadRequest $request)
    {
        $this->validate($request, ['author_id' => 'required|integer']);

        $category = Category::find($request->input('category_id'));

        $this->authorize('createThreads', $category);

        if (!$category->threadsAllowed) {
            return $this->buildFailedValidationResponse(
                $request,
                ['category_id' => "The specified category does not allow threads."]
            );
        }

        $thread = $this->model->create($request->only(['category_id', 'author_id', 'title']));
        Post::create(['thread_id' => $thread->id] + $request->only('author_id', 'content'));

        return $this->response($thread, 201);
    }

    /**
     * DELETE: Delete a thread by ID.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, ['id' => 'required']);

        $thread = $this->model->find($request->input('id'));

        $this->authorize('deleteThreads', $thread->category);

        return parent::destroy($request);
    }

    /**
     * PATCH: Restore a thread by ID.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function restore(Request $request)
    {
        $this->validate($request, ['id' => 'required']);

        $thread = $this->model->withTrashed()->find($request->input('id'));

        $this->authorize('deleteThreads', $thread->category);

        return parent::restore($request);
    }

    /**
     * GET: return an index of new/updated threads for the current user, optionally filtered by category ID.
     *
     * @return JsonResponse|Response
     */
    public function indexNew()
    {
        $this->validate(['category_id' => 'integer|exists:forum_categories,id']);

        $threads = $this->model->recent();

        if ($request->has('category_id')) {
            $threads = $threads->where('category_id', $request->input('category_id'));
        }

        // If the user is logged in, filter the threads according to read status
        if (auth()->check()) {
            $threads = $threads->filter(function ($thread)
            {
                return $thread->userReadStatus;
            });
        }

        // Filter the threads according to the user's permissions
        $threads = $threads->filter(function ($thread)
        {
            return Gate::allows('view', $thread->category);
        });

        $threads = $this->model->where('category_id', $request->input('category_id'))->get();

        return $this->response($threads);
    }

    /**
     * PATCH: mark the current user's new/updated threads as read, optionally limited by category ID.
     *
     * @return JsonResponse|Response
     */
    public function markNew()
    {
        $threads = $this->indexNew();

        if (auth()->check()) {
            foreach ($threads as $thread) {
                $thread->markAsRead(auth()->user()->id);
            }

            $threads = $this->indexNew();
        }

        return $this->response($threads, $this->trans('marked_read'));
    }

    /**
     * PATCH: Move a thread.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function move(Request $request)
    {
        $thread = $this->model->find($request->input('id'));
        $category = Category::find($request->input('destination_category'));

        if ($thread && $category) {
            $this->authorize('moveThreads', $category);
            $thread->category_id = $category->id;
            $thread->save();

            return $this->response($thread, $this->trans('updated'));
        }

        return $this->notFoundResponse();
    }

    /**
     * PATCH: Lock a thread.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function lock(Request $request)
    {
        $thread = $this->model->where('locked', 0)->find($request->input('id'));
        return $this->updateAttributes($thread, 'lockThreads', ['locked' => 1]);
    }

    /**
     * PATCH: Unlock a thread.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function unlock(Request $request)
    {
        $thread = $this->model->where('locked', 1)->find($request->input('id'));
        return $this->updateAttributes($thread, 'lockThreads', ['locked' => 0]);
    }

    /**
     * PATCH: Pin a thread.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function pin(Request $request)
    {
        $thread = $this->model->where('pinned', 0)->find($request->input('id'));
        return $this->updateAttributes($thread, 'pinThreads', ['pinned' => 1]);
    }

    /**
     * PATCH: Unpin a thread.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function unpin(Request $request)
    {
        $thread = $this->model->where('pinned', 1)->find($request->input('id'));
        return $this->updateAttributes($thread, 'pinThreads', ['pinned' => 0]);
    }

    /**
     * DELETE: Delete threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkDestroy(Request $request)
    {
        $this->validate($request, ['threads' => 'required']);

        $threads = collect();
        foreach ($request->input('threads') as $id) {
            $request->replace(compact('id'));
            $response = $this->destroy($request);

            if (!$response->isNotFound()) {
                $threads->push($response->getOriginalContent());
            }
        }

        return $this->response($threads, $this->trans('deleted', $threads->count()));
    }

    /**
     * PATCH: Restore threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkRestore(Request $request)
    {
        $this->validate($request, ['threads' => 'required']);

        $threads = collect();
        foreach ($request->input('threads') as $id) {
            $request->replace(compact('id'));
            $response = $this->restore($request);

            if (!$response->isNotFound()) {
                $threads->push($response->getOriginalContent());
            }
        }

        return $this->response($threads, $this->trans('updated', $threads->count()));
    }

    /**
     * PATCH: Move threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkMove(Request $request)
    {
        $this->validate($request, ['threads' => 'required']);

        $threads = collect();
        foreach ($request->input('threads') as $id) {
            $request->replace($request->only('destination_category') + compact('id'));
            $response = $this->move($request);

            if (!$response->isNotFound()) {
                $threads->push($response->getOriginalContent());
            }
        }

        return $this->response($threads, $this->trans('updated', $threads->count()));
    }

    /**
     * PATCH: Lock threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkLock(Request $request)
    {
        $this->validate($request, ['threads' => 'required']);

        $threads = collect();
        foreach ($request->input('threads') as $id) {
            $request->replace(compact('id'));
            $response = $this->lock($request);

            if (!$response->isNotFound()) {
                $threads->push($response->getOriginalContent());
            }
        }

        return $this->response($threads, $this->trans('updated', $threads->count()));
    }

    /**
     * PATCH: Unlock threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkUnlock(Request $request)
    {
        $this->validate($request, ['threads' => 'required']);

        $threads = collect();
        foreach ($request->input('threads') as $id) {
            $request->replace(compact('id'));
            $response = $this->unlock($request);

            if (!$response->isNotFound()) {
                $threads->push($response->getOriginalContent());
            }
        }

        return $this->response($threads, $this->trans('updated', $threads->count()));
    }

    /**
     * PATCH: Pin threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkPin(Request $request)
    {
        $this->validate($request, ['threads' => 'required']);

        $threads = collect();
        foreach ($request->input('threads') as $id) {
            $request->replace(compact('id'));
            $response = $this->pin($request);

            if (!$response->isNotFound()) {
                $threads->push($response->getOriginalContent());
            }
        }

        return $this->response($threads, $this->trans('updated', $threads->count()));
    }

    /**
     * PATCH: Unpin threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkUnpin(Request $request)
    {
        $this->validate($request, ['threads' => 'required']);

        $threads = collect();
        foreach ($request->input('threads') as $id) {
            $request->replace(compact('id'));
            $response = $this->unpin($request);

            if (!$response->isNotFound()) {
                $threads->push($response->getOriginalContent());
            }
        }

        return $this->response($threads, $this->trans('updated', $threads->count()));
    }

    /**
     * Helper: Update a a given thread's attributes, or return a 404 if no
     * thread given.
     *
     * @param  Thread|null  $thread
     * @param  string  $ability
     * @param  array  $attributes
     * @return JsonResponse|Response
     */
    private function updateAttributes($thread, $ability, array $attributes)
    {
        if ($thread) {
            $this->authorize($ability, $thread->category);
            $thread->timestamps = false;
            $thread->update($attributes);
            $thread->timestamps = true;

            return $this->response($thread, $this->trans('updated'));
        }

        return $this->notFoundResponse();
    }

}
