<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Actions\Bulk\RestorePosts as Action;
use TeamTeaTime\Forum\Events\UserBulkRestoredPosts;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class RestorePosts extends FormRequest implements FulfillableRequest
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
        $posts = Post::whereIn('id', $this->validated()['posts'])->onlyTrashed()->get();
        foreach ($posts as $post)
        {
            if (! $this->user()->can('restore', $post)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $action = new Action($this->validated()['posts']);
        $posts = $action->execute();

        if (! is_null($posts))
        {
            event(new UserBulkRestoredPosts($this->user(), $posts));
        }

        return $posts;
    }
}
