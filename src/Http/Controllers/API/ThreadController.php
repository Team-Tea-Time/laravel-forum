<?php namespace Riari\Forum\Http\Controllers\API;

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
        $this->validate($request, ['category_id' => ['required']]);

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
        $thread = $this->model();
        $thread = $request->input('include_deleted') ? $thread->withTrashed()->find($id) : $thread->find($id);

        if (is_null($thread) || !$thread->exists) {
            return $this->notFoundResponse();
        }

        if ($thread->trashed()) {
            $this->authorize('delete', $thread);
        }

        if ($thread->category->private) {
            $this->authorize('view', $thread->category);
        }

        return $this->response($thread);
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

        if (!$category->threadsEnabled) {
            return $this->buildFailedValidationResponse($request, trans('forum::validation.category_threads_enabled'));
        }

        $thread = $this->model()->create($request->only(['category_id', 'author_id', 'title']));
        Post::create(['thread_id' => $thread->id] + $request->only('author_id', 'content'));

        return $this->response($thread, 201);
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
     * GET: Return an index of new/updated threads for the current user, optionally filtered by category ID.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function indexNew(Request $request)
    {
        $this->validate($request);

        $threads = $this->model()->recent();

        if ($request->has('category_id')) {
            $threads = $threads->where('category_id', $request->input('category_id'));
        }

        $threads = $threads->get();

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
            return (!$thread->category->private || Gate::allows('view', $thread->category));
        });

        return $this->response($threads);
    }

    /**
     * PATCH: Mark the current user's new/updated threads as read, optionally limited by category ID.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function markNew(Request $request)
    {
        $this->authorize('markNewThreadsAsRead');

        $threads = $this->indexNew($request)->getOriginalContent();

        $primaryKey = auth()->user()->getKeyName();
        $userID = auth()->user()->{$primaryKey};

        $threads->transform(function ($thread) use ($userID)
        {
            return $thread->markAsRead($userID);
        });

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
        $this->validate($request, ['category_id' => ['required']]);

        $thread = $this->model()->find($id);

        $category = Category::find($request->input('category_id'));

        if (!$category->threadsEnabled) {
            return $this->buildFailedValidationResponse($request, trans('forum::validation.category_threads_enabled'));
        }

        return $this->updateModel($thread, ['category_id' => $category->id], ['moveThreadsTo', $category]);
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

        $category = !is_null($thread) ? $thread->category : [];

        return $this->updateModel($thread, ['locked' => 1], ['lockThreads', $category]);
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

        $category = !is_null($thread) ? $thread->category : [];

        return $this->updateModel($thread, ['locked' => 0], ['lockThreads', $category]);
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

        $category = !is_null($thread) ? $thread->category : [];

        return $this->updateModel($thread, ['pinned' => 1], ['pinThreads', $category]);
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

        $category = ($thread) ? $thread->category : [];

        return $this->updateModel($thread, ['pinned' => 0], ['pinThreads', $category]);
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

        return $this->updateModel($thread, ['title' => $request->input('title')], 'rename');
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
