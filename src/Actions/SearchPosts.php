<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;

class SearchPosts extends BaseAction
{
    private ?Category $category;
    private string $term;

    public function __construct(?Category $category, string $term)
    {
        $this->category = $category;
        $this->term = $term;
    }

    protected function transact()
    {
        $query = Post::orderBy('created_at', 'DESC')->with('thread', 'thread.category');

        if (isset($this->category))
        {
            $category = $this->category;

            $query = $query->whereHas('thread', function ($query) use ($category)
            {
                $query->whereHas('category', function ($query) use ($category)
                {
                    $query->where('id', $category->id);
                });
            });
        }

        $posts = $query->where('content', 'like', "%{$this->term}%")->paginate();

        $threadIds = $posts->getCollection()->pluck('thread')->filter(function ($thread)
        {
            return ! $thread->category->is_private || Gate::allows('view', $thread->category) && Gate::allows('view', $thread);
        })->pluck('id')->unique();

        $posts->setCollection($posts->getCollection()->filter(function ($post) use ($threadIds)
        {
            return $threadIds->contains($post->thread->id);
        }));

        return $posts;
    }
}