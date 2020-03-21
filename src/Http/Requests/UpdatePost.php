<?php

namespace TeamTeaTime\Forum\Http\Requests;

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

        return $category;
    }
}
