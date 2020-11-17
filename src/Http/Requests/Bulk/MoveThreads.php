<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserBulkMovedThreads;
use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\BaseModel;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class MoveThreads extends BaseRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    private Collection $sourceCategories;
    private Category $destinationCategory;

    public function rules(): array
    {
        return [
            'threads' => ['required', 'array'],
            'category_id' => ['required', 'int', 'exists:forum_categories,id']
        ];
    }

    public function authorizeValidated(): bool
    {
        if (! $this->user()->can('moveThreadsTo', $this->getDestinationCategory())) return false;

        foreach ($this->getSourceCategories() as $category)
        {
            if (! $this->user()->can('moveThreadsFrom', $category)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $threads = $this->threads()->get();
        $threadsByCategory = $threads->groupBy('category_id');
        $sourceCategories = $this->getSourceCategories();
        $destinationCategory = $this->getDestinationCategory();

        $this->threads()->update(['category_id' => $this->validated()['category_id']]);

        foreach ($sourceCategories as $category)
        {
            $categoryThreads = $threadsByCategory->get($category->id);
            $threadCount = $categoryThreads->count();
            $postCount = $threadCount + $categoryThreads->sum('reply_count');
            $category->updateWithoutTouch([
                'newest_thread_id' => $category->getNewestThreadId(),
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
                'thread_count' => DB::raw("thread_count - {$threadCount}"),
                'post_count' => DB::raw("post_count - {$postCount}")
            ]);
        }

        $threadCount = $threads->count();
        $postCount = $threads->count() + $threads->sum('reply_count');
        $destinationCategory->updateWithoutTouch([
            'newest_thread_id' => $destinationCategory->getNewestThreadId(),
            'latest_active_thread_id' => $destinationCategory->getLatestActiveThreadId(),
            'thread_count' => DB::raw("thread_count + {$threadCount}"),
            'post_count' => DB::raw("post_count + {$postCount}")
        ]);

        event(new UserBulkMovedThreads($this->user(), $threads, $sourceCategories, $destinationCategory));

        return $threads;
    }

    private function threads(): Builder
    {
        // Don't include threads that are already in the destination category
        $query = Thread::where('category_id', '!=', $this->validated()['category_id']);

        if (! $this->user()->can('viewTrashedThreads'))
        {
            $query = $query->whereNull(Thread::DELETED_AT);
        }

        return $query->whereIn('id', $this->validated()['threads']);
    }

    private function getSourceCategories()
    {
        if (! $this->sourceCategories)
        {
            $query = Thread::select('category_id')->distinct()->where('category_id', '!=', $this->validated()['category_id']);

            if (! $this->user()->can('viewTrashedThreads'))
            {
                $query = $query->whereNull(Thread::DELETED_AT);
            }

            $this->sourceCategories = Category::whereIn('id', $query->get()->pluck('category_id'));
        }
        
        return $this->sourceCategories;
    }

    private function getDestinationCategory()
    {
        if (! $this->destinationCategory)
        {
            $this->destinationCategory = Category::find($this->validated()['category_id']);
        }
        
        return $this->destinationCategory;
    }
}
