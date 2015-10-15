<?php

namespace Riari\Forum\Models\Observers;

class CategoryObserver extends BaseObserver
{
    public function deleted($category)
    {
        // Delete the threads inside the category
        if ($category->deleted_at != $this->carbon->now()) {
            // The category was permanently deleted, so any threads it contains (including soft-deleted) should be
            // permanently deleted too
            $category->threads()->withTrashed()->forceDelete();
        } else {
            // The category was soft-deleted, so soft-delete any threads it contains
            $category->threads()->delete();
        }
    }

    public function restored($category)
    {
        // Restore any soft-deleted threads inside the category
        $category->threads()->withTrashed()->restore();
    }
}
