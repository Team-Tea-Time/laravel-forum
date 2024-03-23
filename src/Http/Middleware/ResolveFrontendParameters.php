<?php

namespace TeamTeaTime\Forum\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;

class ResolveFrontendParameters
{
    /**
     * Resolve forum frontend route parameters.
     * This logic was originally applied using Route::bind, but moved here to scope it to the package routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $parameters = $request->route()->parameters();

        if (array_key_exists('category_id', $parameters)) {
            $category = Category::find($parameters['category_id']);

            if ($category === null) {
                throw new NotFoundHttpException("Failed to resolve 'category' route parameter.");
            }

            $request->route()->setParameter('category', $category);
        }

        if (array_key_exists('thread_id', $parameters)) {
            $query = Thread::with('category');

            if (Gate::allows('viewTrashedThreads')) {
                $query->withTrashed();
            }

            $thread = $query->find($parameters['thread_id']);

            if ($thread === null) {
                throw new NotFoundHttpException("Failed to resolve 'thread' route parameter.");
            }

            $request->route()->setParameter('thread', $thread);
        }

        if (array_key_exists('post_id', $parameters)) {
            $query = Post::with(['thread', 'thread.category']);

            if (Gate::allows('viewTrashedPosts')) {
                $query->withTrashed();
            }

            $post = $query->find($parameters['post_id']);

            if ($post === null) {
                throw new NotFoundHttpException("Failed to resolve 'post' route parameter.");
            }

            $request->route()->setParameter('post', $post);
        }

        return $next($request);
    }
}
