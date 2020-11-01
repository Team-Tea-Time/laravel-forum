<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    private function isPermaDeleteRequested(): bool
    {
        return isset($this->validated()['permadelete']) && $this->validated()['permadelete'];
    }

    private function isPermaDeleting(): bool
    {
        return ! config('forum.general.soft_deletes') || $this->isPermaDeleteRequested();
    }
}