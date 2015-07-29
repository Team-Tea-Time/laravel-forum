<?php namespace Riari\Forum\Models\Observers;

class CategoryObserver extends BaseObserver
{
    public function deleting($model)
    {
        if (!$this->softDeletes) {
            $model->threads()->forceDelete();
        } else {
            $model->threads()->delete();
        }
    }

    public function restored($model)
    {
        if ($this->softDeletes) {
            $model->threads()->withTrashed()->restore();
        }
    }
}
