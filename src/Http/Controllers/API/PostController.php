<?php

namespace Riari\Forum\API\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class PostController extends BaseController
{
    /**
     * Create a new Category API controller instance.
     *
     * @param  Post  $model
     */
    public function __construct(Post $model)
    {
        $this->model = $model;

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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $this->validate($request, ['thread_id' => 'integer|required|exists:forum_threads,id']);

        $posts = $this->model->where('thread_id', $request->input('thread_id'))->get();

        return $this->collectionResponse($posts);
    }

    /**
     * POST: create a new post.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // For regular frontend requests, thread_id and author_id are set
        // automatically using the current thread and user, so they're not
        // required parameters. For this endpoint, they're set manually, so we
        // need to make them required.
        $this->validate(
            $request,
            array_merge_recursive($this->rules['store'], ['thread_id' => ['integer|required|exists:forum_threads,id'], 'author_id' => ['required']])
        );

        $this->authorize('reply', Thread::find($request->input('thread_id')));

        $post = $this->model->create($request->only(['thread_id', 'author_id', 'title', 'content']));
        $post->load('thread');

        return $this->modelResponse($post, $this->trans('created'), 201);
    }
}
