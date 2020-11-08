<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Events\UserUpdatedPost;

class UpdatePost extends StorePost
{
    public function authorize(): bool
    {
        $post = $this->route('post');
        return $this->user()->can('edit', $post);
    }

    public function fulfill()
    {
        $category = $this->route('post');
        $category->fill($this->validated())->save();

        event(new UserUpdatedPost($this->user(), $category));

        return $category;
    }
}
