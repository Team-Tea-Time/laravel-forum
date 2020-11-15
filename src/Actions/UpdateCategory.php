<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Category;

class UpdateCategory extends BaseAction
{
    private Category $category;
    private ?string $title;
    private ?string $description;
    private ?string $color;
    private ?bool $acceptsThreads;
    private ?bool $isPrivate;

    public function __construct(Category $category, ?string $title, ?string $description, ?string $color, ?bool $acceptsThreads, ?bool $isPrivate)
    {
        $this->category = $category;
        $this->title = $title;
        $this->description = $description;
        $this->color = $color;
        $this->acceptsThreads = $acceptsThreads;
        $this->isPrivate = $isPrivate;
    }

    protected function transact()
    {
        $attributes = [];

        if (! is_null($this->title)) $attributes['title'] = $this->title;
        if (! is_null($this->description)) $attributes['description'] = $this->description;
        if (! is_null($this->color)) $attributes['color'] = $this->color;
        if (! is_null($this->acceptsThreads)) $attributes['accepts_threads'] = $this->acceptsThreads;
        if (! is_null($this->isPrivate)) $attributes['is_private'] = $this->isPrivate;

        $this->category->update($attributes);

        return $this->category;
    }
}