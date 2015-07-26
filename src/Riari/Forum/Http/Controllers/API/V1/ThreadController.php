<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Repositories\Categories;
use Riari\Forum\Repositories\Posts;
use Riari\Forum\Repositories\Threads;

class ThreadController extends BaseController
{
    /**
     * @var Threads
     */
    protected $repository;

    /**
     * @var Categories
     */
    protected $categories;

    /**
     * @var Posts
     */
    protected $posts;

    /**
     * Create a new Category API controller instance.
     *
     * @param  Categories  $categories
     * @param  Threads  $threads
     * @param  Posts  $posts
     */
    public function __construct(Categories $categories, Threads $threads, Posts $posts)
    {
        $this->repository = $threads;
        $this->categories = $categories;
        $this->posts = $posts;

        $rules = config('forum.preferences.validation');
        $this->rules = [
            'store' => array_merge_recursive(
                $rules['base'],
                $rules['post|put']['thread'],
                $rules['post|put']['post']
            ),
            'update' => array_merge_recursive(
                $rules['base'],
                $rules['patch']['thread'],
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
        $v = $this->validate($request, ['category_id' => 'integer|required|exists:forum_categories,id']);

        if ($v instanceof JsonResponse) {
            return $v;
        }

        $posts = $this->threads->findBy('category_id', $request->input('category_id'));

        return $this->collectionResponse($posts);
    }

    /**
     * POST: create a new thread.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // Remove the thread_id validation rules for this request because
        // we'll get a new thread ID automatically.
        unset($this->rules['store']['thread_id']);

        $v = $this->validate($request, $this->rules['store']);

        if ($v instanceof JsonResponse) {
            return $v;
        }

        $category = $this->repository->find($request->input('category_id'));

        if (!$category->threadsAllowed) {
            return $this->buildFailedValidationResponse(
                $request,
                ['category_id' => "The specified category does not allow threads."]
            );
        }

        $thread = $this->threads->create($request->all());
        $this->posts->create($request->all() + ['thread_id' => $thread->id]);

        return $this->modelResponse($thread);
    }
}
