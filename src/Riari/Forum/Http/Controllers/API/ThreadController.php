<?php namespace Riari\Forum\Http\Controllers\API;

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
     */
    public function __construct(Thread $model)
    {
        $this->model = $model;

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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $this->validate($request, ['category_id' => 'required|integer|exists:forum_categories,id']);

        $threads = $this->model->where('category_id', $request->input('category_id'))->get();

        return $this->collectionResponse($threads);
    }

    /**
     * POST: create a new thread.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // For regular frontend requests, author_id is set automatically using
        // the current user, so it's not a required parameter. For this
        // endpoint, it's set manually, so we need to make it required.
        $this->validate(
            $request,
            array_merge_recursive($this->rules['store'], ['author_id' => ['required']])
        );

        $category = Category::find($request->input('category_id'));

        if (!$category->threadsAllowed) {
            return $this->buildFailedValidationResponse(
                $request,
                ['category_id' => "The specified category does not allow threads."]
            );
        }

        $thread = $this->model->create($request->only(['category_id', 'author_id', 'title']));
        Post::create(['thread_id' => $thread->id] + $request->only('content'));

        return $this->modelResponse($thread, 201);
    }
}
