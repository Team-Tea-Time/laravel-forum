<?php

namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Riari\Forum\Models\Thread;
use Riari\Forum\Models\Traits\HasSlug;

class Category extends BaseModel
{
    use SoftDeletes, HasSlug;

    // Eloquent properties
    protected $table        = 'forum_categories';
    protected $fillable     = ['category_id', 'title', 'subtitle', 'weight', 'allows_threads'];
    public    $timestamps   = false;

    /**
     * Create a new category model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('forum.preferences.pagination.categories');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function parent()
    {
        return $this->belongsTo('\Riari\Forum\Models\Category', 'category_id')->orderBy('weight');
    }

    public function children()
    {
        return $this->hasMany('\Riari\Forum\Models\Category', 'category_id')->orderBy('weight');
    }

    public function threads()
    {
        return $this->hasMany('\Riari\Forum\Models\Thread');
    }

    /**
     * Relationship: Threads (including soft-deleted).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function threadsWithTrashed()
    {
        return $this->threads()->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    // Route attributes

    public function getRouteAttribute()
    {
        return $this->buildRoute('forum.category.index');
    }

    public function getNewThreadRouteAttribute()
    {
        return $this->buildRoute('forum.thread.create');
    }

    // General attributes

    public function getThreadsPaginatedAttribute()
    {
        return $this->threads()
            ->orderBy('pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(config('forum.preferences.pagination.threads'));
    }

    public function getThreadsWithTrashedPaginatedAttribute()
    {
        return $this->threadsWithTrashed()
            ->orderBy('pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(config('forum.preferences.pagination.threads'));
    }

    public function getPageLinksAttribute()
    {
        return $this->threadsPaginated->render();
    }

    public function getNewestThreadAttribute()
    {
        return $this->threads()->orderBy('created_at', 'desc')->first();
    }

    public function getLatestActiveThreadAttribute()
    {
        return $this->threads()->orderBy('updated_at', 'desc')->first();
    }

    public function getThreadsAllowedAttribute()
    {
        return $this->allows_threads;
    }

    public function getThreadCountAttribute()
    {
        return $this->rememberAttribute('threadCount', function()
        {
            return $this->threads->count();
        });
    }

    public function getPostCountAttribute()
    {
        return $this->rememberAttribute('postCount', function()
        {
            $replyCount = 0;

            $threads = $this->threads()->get(['id']);

            foreach ($threads as $thread) {
                $replyCount += $thread->posts->count() - 1;
            }

            return $replyCount;
        });
    }

    /**
     * Helper: Get route parameters.
     *
     * @return array
     */
    public function getRouteParameters()
    {
        return [
            'category'      => $this->id,
            'category_slug' => $this->slug
        ];
    }
}
