<?php

namespace TeamTeaTime\Forum\Http\Requests;

class UpdatePost extends StorePost
{
    public function fulfill()
    {
        $category = $this->route('post');
        $category->fill($this->validated())->save();

        return $category;
    }
}
