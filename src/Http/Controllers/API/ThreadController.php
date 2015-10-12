<?php

namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class ThreadController extends BaseController
{
    /**
     * Create a new Category API controller instance.
     *
     * @param  Thread  $model
     * @param  Request  $request
     */
    public function __construct(Thread $model, Request $request)
    {
        parent::__construct($model, $request);

        $rules = config('forum.preferences.validation');
        $this->rules = [
            'store' => array_merge_recursive(
                $rules['base'],
                $rules['post|put']['thread'],
                $rules['post|put']['post']
            ),
            'update' => array_merge_recursive(
                $rules['base'],
                $rules['patch']['thread']
            )
        ];

        $this->translationFile = 'threads';
    }

    /**
     * GET: return an index of threads by category ID.
     *
     * @return JsonResponse|Response
     */
    public function index()
    {
        $this->validate(['category_id' => 'required|integer|exists:forum_categories,id']);

        $threads = $this->model->where('category_id', $this->request->input('category_id'))->get();

        return $this->collectionResponse($threads);
    }

    /**
     * POST: create a new thread.
     *
     * @return JsonResponse|Response
     */
    public function store()
    {
        // For regular frontend requests, author_id is set automatically using
        // the current user, so it's not a required parameter. For this
        // endpoint, it's set manually, so we need to make it required.
        $this->validate(
            array_merge_recursive($this->rules['store'], ['author_id' => ['required']])
        );

        $category = Category::find($this->request->input('category_id'));

        $this->authorize('createThreads', $category);

        if (!$category->threadsAllowed) {
            return $this->buildFailedValidationResponse(
                $this->request,
                ['category_id' => "The specified category does not allow threads."]
            );
        }

        $thread = $this->model->create($this->request->only(['category_id', 'author_id', 'title']));
        Post::create(['thread_id' => $thread->id] + $this->request->only('content'));

        return $this->modelResponse($thread, 201);
    }

    /**
     * GET: return a thread by ID.
     *
     * @param  int  $id
     * @return JsonResponse|Response
     */
    public function show($id)
    {
        $model = $this->model;

        if (
            $this->request->input('include_deleted') &&
            config('forum.preferences.list_trashed_posts') &&
            $this->request->user()->can('deletePosts', $model)
        ) {
            $model = $model->with('postsWithTrashed');
        }

        $model = $model->find($id);

        if (is_null($model) || !$model->exists) {
            return $this->notFoundResponse();
        }

        return $this->modelResponse($model);
    }
}
