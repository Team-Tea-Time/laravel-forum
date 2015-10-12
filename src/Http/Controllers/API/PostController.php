<?php

namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class PostController extends BaseController
{
    /**
     * Create a new Post API controller instance.
     *
     * @param  Post  $model
     * @param  Request  $request
     */
    public function __construct(Post $model, Request $request)
    {
        parent::__construct($model, $request);

        $rules = config('forum.preferences.validation');
        $this->rules = [
            'store' => array_merge_recursive(
                $rules['base'],
                $rules['post|put']['post']
            ),
            'update' => array_merge_recursive(
                $rules['base'],
                $rules['patch']['post']
            )
        ];

        $this->translationFile = 'posts';
    }

    /**
     * GET: return an index of posts by thread ID.
     *
     * @return JsonResponse|Response
     */
    public function index()
    {
        $this->validate(['thread_id' => 'integer|required|exists:forum_threads,id']);

        $posts = $this->model->where('thread_id', $this->request->input('thread_id'))->get();

        return $this->collectionResponse($posts);
    }

    /**
     * POST: create a new post.
     *
     * @return JsonResponse|Response
     */
    public function store()
    {
        // For regular frontend requests, thread_id and author_id are set
        // automatically using the current thread and user, so they're not
        // required parameters. For this endpoint, they're set manually, so we
        // need to make them required.
        $this->validate(
            array_merge_recursive($this->rules['store'], ['thread_id' => ['integer', 'required', 'exists:forum_threads,id'], 'author_id' => ['required']])
        );

        $thread = Thread::find($this->request->input('thread_id'));
        $this->authorize('reply', $thread);

        $post = $this->model->create($this->request->only(['thread_id', 'post_id', 'author_id', 'title', 'content']));
        $post->load('thread');

        return $this->modelResponse($post, $this->trans('created'), 201);
    }
}
