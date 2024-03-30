<?php

namespace TeamTeaTime\Forum\Support\Validation;

class CategoryRules
{
    public static function create(): array
    {
        return [
            'title' => ['required', 'string', 'min:' . config('forum.general.validation.title_min')],
            'description' => ['nullable', 'string'],
            'color_light_mode' => ['string'],
            'color_dark_mode' => ['string'],
            'parent_category' => ['nullable', 'int', 'exists:forum_categories,id'],
            'accepts_threads' => ['boolean'],
            'is_private' => ['boolean'],
        ];
    }

    public static function markThreadsAsRead(): array
    {
        return [
            'category_id' => ['int', 'exists:forum_categories,id'],
        ];
    }

    public static function delete(): array
    {
        return [
            'force' => ['boolean'],
        ];
    }

    public static function bulk(): array
    {
        return [
            'categories' => ['required', 'array'],
        ];
    }
}
