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
     * GET: return a thread by ID.
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
     * POST: create a new thread.
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
     */
    public function move(Request $request)
    {
        $thread = $this->model->find($request->input('id'));
        $category = Category::find($request->input('category'));

        if ($thread && $category) {
            $this->authorize('moveThreads', $category);

            $thread->category_id = $category->id;
            $thread->save();
        }

        return $this->response($thread, $this->trans('updated'));
    }

    /**
     * PATCH: Move threads in bulk.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function bulkMove(Request $request)
    {
        $input = $request->except('threads');
        $threadIDs = $request->input('threads');

        $threads = collect();
        foreach ($threadIDs as $id) {
            $request->replace($input + ['id' => $id]);
            $thread = $this->move($request);
            $threads->push($thread);
        }

        return $this->response($threads, $this->trans('threads_updated', $threads->count()));
    }
}
