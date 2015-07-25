<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Models\Post;

class PostController extends BaseController
{
    /**
     * GET: return an index of posts by thread ID.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $v = $this->validate($request, ['thread_id' => 'integer|required|exists:forum_threads,id']);

        if ($v instanceof JsonResponse) {
            return $v;
        }

        $posts = $this->posts->findBy('thread_id', $request->input('thread_id'));

        return $this->collectionResponse($posts);
    }
}
