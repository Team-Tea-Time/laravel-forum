<?php

namespace Riari\Forum\Models\Observers;

class CategoryObserver
{
    public function deleted($model)
    {
        if (!$model->withTrashed()->exists) {
            $model->threads()->forceDelete();
        } else {
            $model->threads()->delete();
        }
    }

    public function restored($model)
    {
        $model->threads()->withTrashed()->restore();
    }
}
