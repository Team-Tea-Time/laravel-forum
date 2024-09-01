<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Category;

class EditCategory extends CreateCategory
{
    protected Category $category;

    public function __construct(Category $category, string $title, string $description, string $colorLightMode, string $colorDarkMode, bool $acceptsThreads = true, bool $isPrivate = false, bool $threadQueueEnabled = false, bool $postQueueEnabled = false)
    {
        parent::__construct($title, $description, $colorLightMode, $colorDarkMode, $acceptsThreads, $isPrivate, $threadQueueEnabled, $postQueueEnabled);
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
            'thread_queue_enabled' => $this->threadQueueEnabled,
            'post_queue_enabled' => $this->postQueueEnabled
        ]);

        // TODO: when the thread queue is enabled, any existing threads that don't have an approved_at value should probably be given one. Same for posts.

        return $this->category;
    }
}
