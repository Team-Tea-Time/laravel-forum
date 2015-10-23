<?php

namespace Riari\Forum\Models\Observers;

class CategoryObserver extends BaseObserver
{
    public function deleted($category)
    {
        // Delete the categories and threads inside the category
        if ($category->deleted_at != $this->carbon->now()) {
            // The category was permanently deleted, so any categories and threads it contains (including soft-deleted) should be permanently deleted too
            $category->children()->withTrashed()->forceDelete();
            $category->threads()->withTrashed()->forceDelete();
        } else {
            // The category was soft-deleted, so soft-delete any categories and threads it contains
            $category->children()->delete();
            $category->threads()->delete();
        }
    }

    public function restored($category)
    {
        // Restore any soft-deleted categories threads inside the category
        $category->children()->withTrashed()->restore();
        $category->threads()->withTrashed()->restore();
    }
}
