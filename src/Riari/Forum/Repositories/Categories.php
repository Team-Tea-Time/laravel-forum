<?php namespace Riari\Forum\Repositories;

use Riari\Forum\Models\Category;

class Categories extends BaseRepository
{
    /**
     * Create a new category repository instance.
     *
     * @param  Category  $model
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
        $this->perPage = config('forum.preferences.pagination.categories');
    }

    /**
     * Get the top level categories (i.e. those where category_id == null).
     *
     * @param  array  $columns
     * @return Collection
     */
    public function getTop($columns = ['*'])
    {
        return $this->model->where('category_id', null)->get($columns);
    }
}
