<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Support\Facades\DB;
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
        $destinationCategory = $this->getDestinationCategory();

        $thread->updateWithoutTouch(['category_id' => $destinationCategory->id]);

        $sourceCategoryValues = [];

        if ($sourceCategory->newest_thread_id === $thread->id)
        {
            $sourceCategoryValues['newest_thread_id'] = $sourceCategory->getNewestThreadId();
        }
        if ($sourceCategory->latest_active_thread_id === $thread->id)
        {
            $sourceCategoryValues['latest_active_thread_id'] = $sourceCategory->getLatestActiveThreadId();
        }

        $sourceCategoryValues['thread_count'] = DB::raw('thread_count - 1');
        $sourceCategoryValues['post_count'] = DB::raw("post_count - {$thread->postCount}");

        $sourceCategory->updateWithoutTouch($sourceCategoryValues);

        $destinationCategory->updateWithoutTouch([
            'thread_count' => DB::raw('thread_count + 1'),
            'post_count' => DB::raw("post_count + {$thread->postCount}"),
            'newest_thread_id' => $destinationCategory->getNewestThreadId(),
            'latest_active_thread_id' => $destinationCategory->getLatestActiveThreadId()
        ]);

        event(new UserMovedThread($this->user(), $thread, $sourceCategory, $destinationCategory));

        return $thread;
    }

    private function getDestinationCategory(): Category
    {
        if (! isset($this->destinationCategory))
        {
            $this->destinationCategory = Category::find($this->input('category_id'));
        }
        
        return $this->destinationCategory;
    }
}
