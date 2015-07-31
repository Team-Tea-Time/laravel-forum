<?php namespace Riari\Forum\Models\Observers;

class ThreadObserver
{
    public function deleted($model)
    {
        $model->posts()->delete();
    }

    public function restored($model)
    {
        $model->posts()->withTrashed()->restore();
    }
}
