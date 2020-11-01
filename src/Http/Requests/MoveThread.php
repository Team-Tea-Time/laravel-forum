<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class MoveThread extends BaseRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');
        $targetCategory = Category::find($this->input('category_id'));
        return $this->user()->can('moveThreadsFrom', $thread->category) && $this->user()->can('moveThreadsTo', $targetCategory);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'int', 'exists:forum_categories,id']
        ];
    }

    public function fulfill()
    {
        $thread = $this->route('thread');
        $thread->category_id = $this->input('category_id');
        $thread->saveWithoutTouch();

        return $thread;
    }
}
