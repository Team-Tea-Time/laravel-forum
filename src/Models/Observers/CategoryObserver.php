<?php

namespace Riari\Forum\Models\Observers;

class CategoryObserver extends BaseObserver
{
    public function deleted($model)
    {
        if ($model->deleted_at != $this->carbon->now()) {
            $model->threads()->withTrashed()->forceDelete();
        } else {
            $model->threads()->delete();
        }
    }

    public function restored($model)
    {
        $model->threads()->withTrashed()->restore();
    }
}
