<?php

namespace TeamTeaTime\Forum\Support\Validation;

class PostRules
{
    public static function create(): array
    {
        return [
            'content' => ['required', 'string', 'min:'.config('forum.general.validation.content_min')],
        ];
    }

    public static function search(): array
    {
        return [
            'term' => ['required', 'string'],
        ];
    }

    public static function delete(): array
    {
        return [
            'permadelete' => ['boolean'],
        ];
    }
}
