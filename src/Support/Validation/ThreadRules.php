<?php

namespace TeamTeaTime\Forum\Support\Validation;

class ThreadRules
{
    public static function create(): array
    {
        return [
            'title' => ['required', 'string', 'min:' . config('forum.general.validation.title_min')],
            'content' => ['required', 'string', 'min:' . config('forum.general.validation.content_min')],
        ];
    }

    public static function rename(): array
    {
        return [
            'title' => ['required', 'string', 'min:' . config('forum.general.validation.title_min')],
        ];
    }

    public static function move(): array
    {
        return [
            'category_id' => ['required', 'int', 'exists:forum_categories,id'],
        ];
    }

    public static function delete(): array
    {
        return [
            'permadelete' => ['boolean'],
        ];
    }
}
