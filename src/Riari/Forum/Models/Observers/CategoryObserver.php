<?php namespace Riari\Forum\Models\Observers;

class CategoryObserver
{
    public function deleting($model)
    {
        $model->threads()->delete();
    }

    public function restored($model)
    {
        $model->threads()->withTrashed()->restore();
    }
}
