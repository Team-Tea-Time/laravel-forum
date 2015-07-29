<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Repositories\Posts;

class PostController extends BaseController
{
    /**
     * @var Posts
     */
    protected $posts;

    /**
     * Create a new Category API controller instance.
     *
     * @param  Posts  $posts
     */
    public function __construct(Posts $posts)
    {
        $this->repository = $posts;

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

        $posts = $this->repository->findBy('thread_id', $request->input('thread_id'));

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
            array_merge_recursive($this->rules['store'], ['thread_id' => ['required'], 'author_id' => ['required']])
        );

        $post = $this->repository->create($request->all());
        $post->load('thread');

        return $this->modelResponse($post, 201);
    }
}
