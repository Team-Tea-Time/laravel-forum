<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;

class MarkThreadsAsRead extends BaseAction
{
    private User $user;
    private ?Category $category;

    public function __construct(User $user, ?Category $category)
    {
        $this->user = $user;
        $this->category = $category;
    }

    protected function transact()
    {
        $threads = Thread::recent();

        if ($this->category !== null) {
            $threads = $threads->where('category_id', $this->category->id);
        }

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($this->user);

        $threads = $threads->get()->filter(function ($thread) use ($accessibleCategoryIds) {
            // @TODO: handle authorization check outside of action?
            return $thread->userReadStatus != null
                && (!$thread->category->is_private || ($accessibleCategoryIds->contains($thread->category_id) && $this->user->can('view', $thread)));
        });

        foreach ($threads as $thread) {
            $thread->markAsRead($this->user);
        }

        return $threads;
    }
}
