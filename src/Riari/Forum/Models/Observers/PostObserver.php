<?php namespace Riari\Forum\Models\Observers;

class PostObserver extends BaseObserver
{
    public function deleted($model)
    {
        if ($model->thread->posts->isEmpty()) {
            if (!$this->softDeletes) {
                $model->thread()->forceDelete();
            } else {
                $model->thread()->delete();
            }
        }
    }

    public function restored($model)
    {
        if ($this->softDeletes && is_null($model->thread->posts)) {
            $model->thread()->withTrashed()->restore();
        }
    }
}
