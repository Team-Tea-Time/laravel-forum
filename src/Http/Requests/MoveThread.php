<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class MoveThread extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');
        return $this->user()->can('moveThreadsFrom', $thread->category);
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

        $oldCategory = $thread->category;
        $oldCategory->thread_count -= 1;
        $oldCategory->post_count -= ($thread->reply_count + 1);
        $oldCategory->save();

        $newCategory = Category::find($this->input('category_id'));
        $newCategory->thread_count += 1;
        $newCategory->post_count += ($thread->reply_count + 1);
        $newCategory->save();

        $thread->category_id = $newCategory->id;
        $thread->save();

        return $thread;
    }
}
