<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Events\UserBulkRestoredPosts;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class RestorePosts extends BaseRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return [
            'posts' => ['required', 'array']
        ];
    }

    public function authorizeValidated(): bool
    {
        $posts = $this->posts()->get();
        foreach ($posts as $post)
        {
            if (! $this->user()->can('restore', $post)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $posts = $this->posts();
        $posts->restore();
        
        $postsByThread = $posts->select('thread_id')->distinct()->get();
        foreach ($postsByThread as $post)
        {
            $post->thread->syncLastPost();
            $post->thread->category->syncLatestActiveThread();
        }

        $posts = $posts->get();

        event(new UserBulkRestoredPosts($this->user(), $posts));

        return $posts;
    }

    private function posts(): Builder
    {
        $query = DB::table(Post::getTableName());

        if (! $this->user()->can('viewTrashedPosts'))
        {
            $query = $query->whereNull(Post::DELETED_AT);
        }

        return $query->whereIn('id', $this->validated()['posts']);
    }
}
