<?php namespace TeamTeaTime\Forum\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Kalnoy\Nestedset\NodeTrait;

use TeamTeaTime\Forum\Support\Frontend\Forum;
use TeamTeaTime\Forum\Support\Traits\CachesData;

class Category extends BaseModel
{
    use CachesData, NodeTrait;

    protected $table = 'forum_categories';

    public $timestamps = false;

    protected $fillable = ['title', 'description', 'accepts_threads', 'is_private', 'color', 'thread_count', 'post_count'];

    protected $appends = ['route'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('forum.general.pagination.categories');
    }

    public function threads(): HasMany
    {
        $withTrashed = Gate::allows('viewTrashedThreads');
        $query = $this->hasMany(Thread::class);
        return $withTrashed ? $query->withTrashed() : $query;
    }

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->where('parent_id', 0);
    }

    public function scopeAcceptsThreads(Builder $query): Builder
    {
        return $query->where('accepts_threads', 1);
    }

    public function scopeIsPrivate(Builder $query): Builder
    {
        return $query->where('is_private', 1);
    }

    public function getRouteAttribute(): string
    {
        return Forum::route('category.show', $this);
    }

    public function getThreadsPaginatedAttribute(): LengthAwarePaginator
    {
        return $this->threads()->orderBy('pinned', 'desc')->orderBy('updated_at', 'desc')->paginate();
    }

    public function getNewestThreadAttribute(): ?Thread
    {
        return $this->threads()->orderBy('created_at', 'desc')->first();
    }

    public function getMostRecentlyActiveThreadAttribute(): ?Thread
    {
        return $this->threads()->orderBy('updated_at', 'desc')->first();
    }

    public function getDeepestChildAttribute(): Category
    {
        $category = $this;

        return $this->remember('deepestChild', function () use ($category) {
            while ($category->parent) {
                $category = $category->parent;
            }

            return $category;
        });
    }

    public function getDepthAttribute(): int
    {
        $category = $this;

        return $this->remember('depth', function () use ($category) {
            $depth = 0;

            while ($category->parent) {
                $depth++;
                $category = $category->parent;
            }

            return $depth;
        });
    }
}
