<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Category;

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

        // TODO: when thread approval is enabled, any existing threads that don't have an approved_at value should probably be given one. Same for posts.

        return $this->category;
    }
}
