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
        $v = $this->validate($request, ['thread_id' => 'integer|required|exists:forum_threads,id']);

        if ($v instanceof JsonResponse) {
            return $v;
        }

        $posts = $this->repository->findBy('thread_id', $request->input('thread_id'));

        return $this->collectionResponse($posts);
    }
}
