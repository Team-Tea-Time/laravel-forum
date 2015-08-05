<?php namespace Riari\Forum\Models\Observers;

class ThreadObserver extends BaseObserver
{
    public function deleted($model)
    {
        if (!$model->exists) {
            $model->posts()->forceDelete();
        } else {
            $model->posts()->delete();
        }
    }

    public function restored($model)
    {
        $model->posts()->withTrashed()->restore();
    }
}
