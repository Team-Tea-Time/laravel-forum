<?php namespace Riari\Forum\Models\Observers;

class ThreadObserver extends BaseObserver
{
    public function deleted($model)
    {
        if (!$this->softDeletes) {
            $model->posts()->forceDelete();
        } else {
            $model->posts()->delete();
        }
    }

    public function restored($model)
    {
        if ($this->softDeletes) {
            $model->posts()->withTrashed()->restore();
        }
    }
}
