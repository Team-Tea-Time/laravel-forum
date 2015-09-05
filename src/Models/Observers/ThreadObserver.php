<?php

namespace Riari\Forum\Models\Observers;

class ThreadObserver extends BaseObserver
{
    public function deleted($model)
    {
        if ($model->deleted_at != $this->carbon->now()) {
            $model->posts()->withTrashed()->forceDelete();
        } else {
            $model->posts()->delete();
        }
    }

    public function restored($model)
    {
        $model->posts()->withTrashed()->restore();
    }
}
