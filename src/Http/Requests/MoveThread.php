<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Events\UserMovedThread;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class MoveThread extends BaseRequest implements FulfillableRequest
{
    private Category $destinationCategory;

    public function authorize(): bool
    {
        $thread = $this->route('thread');
        $destinationCategory = $this->getDestinationCategory();
        return $this->user()->can('moveThreadsFrom', $thread->category) && $this->user()->can('moveThreadsTo', $destinationCategory);
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
        $sourceCategory = $thread->category;
        $thread->category_id = $this->input('category_id');
        $thread->saveWithoutTouch();

        event(new UserMovedThread($this->user(), $thread, $sourceCategory, $this->getDestinationCategory()));

        return $thread;
    }

    private function getDestinationCategory(): Category
    {
        if (! $this->destinationCategory)
        {
            $this->destinationCategory = Category::find($this->input('category_id'));
        }
        
        return $this->destinationCategory;
    }
}
