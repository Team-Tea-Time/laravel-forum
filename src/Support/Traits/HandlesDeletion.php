<?php

namespace TeamTeaTime\Forum\Support\Traits;

trait HandlesDeletion
{
    protected function shouldPermaDelete(bool $userRequestedPermaDelete): bool
    {
        $softDeletesEnabled = config('forum.general.soft_deletes');

        return !$softDeletesEnabled || ($softDeletesEnabled && $userRequestedPermaDelete);
    }
}
