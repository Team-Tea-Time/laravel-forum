<?php

namespace TeamTeaTime\Forum\Actions;

use Carbon\Carbon;
use TeamTeaTime\Forum\Models\{
    Category,
    Post,
    Thread,
};

class EditCategory extends CreateCategory
{
    protected Category $category;

    public function __construct(Category $category, string $title, string $description, string $colorLightMode, string $colorDarkMode, bool $acceptsThreads = true, bool $isPrivate = false, bool $threadApprovalEnabled = false, bool $postApprovalEnabled = false)
    {
        parent::__construct($title, $description, $colorLightMode, $colorDarkMode, $acceptsThreads, $isPrivate, $threadApprovalEnabled, $postApprovalEnabled);
        $this->category = $category;
    }

    protected function transact()
    {
        $threadApprovalWasDisabled = $this->category->thread_approval_enabled && !$this->threadApprovalEnabled;
        $postApprovalWasDisabled = $this->category->post_approval_enabled && !$this->postApprovalEnabled;

        if ($threadApprovalWasDisabled || $postApprovalWasDisabled) {
            // TODO: Move these operations to a job so they can be executed async.
            foreach ($this->category->threads as $thread) {
                if ($threadApprovalWasDisabled) {
                    Thread::withoutTimestamps(fn () => $thread->update(['approved_at' => Carbon::now()->subSecond()]));
                }

                if ($postApprovalWasDisabled) {
                    foreach ($thread->posts as $post) {
                        Post::withoutTimestamps(fn () => $post->update(['approved_at' => Carbon::now()->subSecond()]));
                    }
                }
            }
        }

        $this->category->update([
            'title' => $this->title,
            'description' => $this->description,
            'color_light_mode' => $this->colorLightMode,
            'color_dark_mode' => $this->colorDarkMode,
            'accepts_threads' => $this->acceptsThreads,
            'is_private' => $this->isPrivate,
            'thread_approval_enabled' => $this->threadApprovalEnabled,
            'post_approval_enabled' => $this->postApprovalEnabled
        ]);

        return $this->category;
    }
}
