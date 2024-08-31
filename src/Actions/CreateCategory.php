<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Category;

class CreateCategory extends BaseAction
{
    public string $title;
    public string $description;
    public string $colorLightMode;
    public string $colorDarkMode;
    public bool $acceptsThreads;
    public bool $isPrivate;
    public bool $threadQueueEnabled;
    public bool $postQueueEnabled;

    public function __construct(string $title, string $description, string $colorLightMode, string $colorDarkMode, bool $acceptsThreads = true, bool $isPrivate = false, bool $threadQueueEnabled = false, bool $postQueueEnabled = false)
    {
        $this->title = $title;
        $this->description = $description;
        $this->colorLightMode = $colorLightMode;
        $this->colorDarkMode = $colorDarkMode;
        $this->acceptsThreads = $acceptsThreads;
        $this->isPrivate = $isPrivate;
        $this->threadQueueEnabled = $threadQueueEnabled;
        $this->postQueueEnabled = $postQueueEnabled;
    }

    protected function transact()
    {
        return Category::create([
            'title' => $this->title,
            'description' => $this->description,
            'color_light_mode' => $this->colorLightMode,
            'color_dark_mode' => $this->colorDarkMode,
            'accepts_threads' => $this->acceptsThreads,
            'is_private' => $this->isPrivate,
            'thread_queue_enabled' => $this->threadQueueEnabled,
            'post_queue_enabled' => $this->postQueueEnabled
        ]);
    }
}
