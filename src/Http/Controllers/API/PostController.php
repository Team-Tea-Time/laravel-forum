<?php namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class PostController extends BaseController
{
    /**
     * Return the model to use for this controller.
     *
     * @return Post
     */
    protected function model()
    {
        return new Post;
    }

    /**
     * Return the translation file name to use for this controller.
     *
     * @return string
     */
    protected function translationFile()
    {
        return 'posts';
    }

    /**
     * GET: Return an index of posts by thread ID.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function index(Request $request)
    {
        $this->validate($request, ['thread_id' => ['required']]);

        return $this->responseWithQuery(
            $request,
            $this->model()->where('thread_id', $request->input('thread_id')),
            "posts"
        );
    }

    /**
     * Runs the paginate query
     *
     * @param Builder $query
     * @param int     $perPage
     * @param Request $request
     *
     * @return AbstractPaginator
     */
    protected function runPaginateQuery($query, $perPage, $request)
    {
        $childRelations = [
            "children" => function($builder) use ($request) {
                $builder->withRequestScopes($request);
            }
        ];

        // when paginating is enabled, we need to return only parent posts
        // then load recursively the children's
        $query->whereNull("post_id")->with($childRelations);

        $paginator = parent::runPaginateQuery($query, $perPage, $request);

        if (!$paginator->isEmpty()) {
            $this->loadChildrenRecursively($paginator->getCollection(), $childRelations);
        }

        return $paginator;
    }

    /**
     * Loads the posts children recursively via relation
     *
     * @param Collection $items
     * @param array      $childRelations a settings to setup the same scopes for the relation loading
     *
     * @return $this
     */
    protected function loadChildrenRecursively($items, $childRelations)
    {
        if ($items->isEmpty()) {
            return $this;
        }

        /** @var Post $item */
        foreach ($items as $item) {
            if (!$item->relationLoaded("children")) {
                $item->load($childRelations);
            }

            // load the children items
            $this->loadChildrenRecursively($item->children, $childRelations);
        }

        return $this;
    }


    /**
     * GET: Return a post.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function fetch($id, Request $request)
    {
        $post = $this->model()->find($id);

        if (is_null($post) || !$post->exists) {
            return $this->notFoundResponse();
        }

        return $this->response($post);
    }

    /**
     * POST: Create a new post.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['thread_id' => ['required'], 'author_id' => ['required'], 'content' => ['required']]);

        $thread = Thread::find($request->input('thread_id'));
        $this->authorize('reply', $thread);

        $post = $this->model()->create($request->only(['thread_id', 'post_id', 'author_id', 'content']));
        $post->load('thread');

        return $this->response($post, $this->trans('created'), 201);
    }
}
