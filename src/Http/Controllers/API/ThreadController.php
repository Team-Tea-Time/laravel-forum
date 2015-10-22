<?php

namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        $threads = $this->model()
            ->withRequestScopes($request)
            ->where('category_id', $request->input('category_id'))
            ->get();

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
        if ($request->input('include_deleted')) {
            $thread = $this->model()->withTrashed()->find($id);

            if ($thread && Gate::allows('delete', $thread)) {
                return $this->response($thread);
            }
        }

        return parent::fetch($id, $request);
    }

    /**
     * POST: Create a new thread.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'author_id' => ['required', 'integer'],
            'title'     => ['required'],
            'content'   => ['required']
        ]);

        $category = Category::find($request->input('category_id'));

        $this->authorize('createThreads', $category);

        if (!$category->threadsAllowed) {
            return $this->buildFailedValidationResponse(
                $request,
                ['category_id' => "The specified category does not allow threads."]
            );
        }

        $thread = $this->model()->create($request->only(['category_id', 'author_id', 'title']));
        Post::create(['thread_id' => $thread->id] + $request->only('author_id', 'content'));

        return $this->response($thread, 201);
    }

    /**
     * DELETE: Delete a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function destroy($id, Request $request)
    {
        $thread = $this->model()->withTrashed()->find($id);

        $this->authorize('deleteThreads', $thread->category);

        return parent::destroy($id, $request);
    }

    /**
     * PATCH: Restore a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function restore($id, Request $request)
    {
        $thread = $this->model()->withTrashed()->find($id);

        $this->authorize('deleteThreads', $thread->category);

        return parent::restore($id, $request);
    }

    /**
     * GET: return an index of new/updated threads for the current user, optionally filtered by category ID.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function indexNew(Request $request)
    {
        $this->validate(['category_id' => 'integer|exists:forum_categories,id']);

        $threads = $this->model()->recent();

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

        $threads = $this->model()->where('category_id', $request->input('category_id'))->get();

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
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function move($id, Request $request)
    {
        $this->validate($request, ['category_id' => 'required|integer|exists:forum_categories,id']);

        $thread = $this->model()->find($id);

        $category = Category::find($request->input('category_id'));

        return ($thread)
            ? $this->updateAttributes($thread, ['category_id' => $category->id], ['moveThreads', $category])
            : $this->notFoundResponse();
    }

    /**
     * PATCH: Lock a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function lock($id, Request $request)
    {
        $thread = $this->model()->where('locked', 0)->find($id);

        return ($thread)
            ? $this->updateAttributes($thread, ['locked' => 1], ['lockThreads', $thread->category])
            : $this->notFoundResponse();
    }

    /**
     * PATCH: Unlock a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function unlock($id, Request $request)
    {
        $thread = $this->model()->where('locked', 1)->find($id);

        return ($thread)
            ? $this->updateAttributes($thread, ['locked' => 0], ['lockThreads', $thread->category])
            : $this->notFoundResponse();
    }

    /**
     * PATCH: Pin a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function pin($id, Request $request)
    {
        $thread = $this->model()->where('pinned', 0)->find($id);

        return ($thread)
            ? $this->updateAttributes($thread, ['pinned' => 1], ['pinThreads', $thread->category])
            : $this->notFoundResponse();
    }

    /**
     * PATCH: Unpin a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function unpin($id, Request $request)
    {
        $thread = $this->model()->where('pinned', 1)->find($id);

        return ($thread)
            ? $this->updateAttributes($thread, ['pinned' => 0], ['pinThreads', $thread->category])
            : $this->notFoundResponse();
    }

    /**
     * PATCH: Rename a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function rename($id, Request $request)
    {
        $this->validate($request, ['title' => ['required']]);

        $thread = $this->model()->find($id);

        return ($thread)
            ? $this->updateAttributes($thread, ['title' => $request->input('title')], ['rename', $thread])
            : $this->notFoundResponse();
    }

    /**
     * DELETE: Delete threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkDestroy(Request $request)
    {
        return $this->bulk($request, 'destroy', 'updated', $request->only('force'));
    }

    /**
     * PATCH: Restore threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkRestore(Request $request)
    {
        return $this->bulk($request, 'restore', 'updated');
    }

    /**
     * PATCH: Move threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkMove(Request $request)
    {
        return $this->bulk($request, 'move', 'updated', $request->only('category_id'));
    }

    /**
     * PATCH: Lock threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkLock(Request $request)
    {
        return $this->bulk($request, 'lock', 'updated');
    }

    /**
     * PATCH: Unlock threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkUnlock(Request $request)
    {
        return $this->bulk($request, 'unlock', 'updated');
    }

    /**
     * PATCH: Pin threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkPin(Request $request)
    {
        return $this->bulk($request, 'pin', 'updated');
    }

    /**
     * PATCH: Unpin threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkUnpin(Request $request)
    {
        return $this->bulk($request, 'unpin', 'updated');
    }
}
