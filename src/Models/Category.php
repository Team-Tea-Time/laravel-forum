<?php namespace TeamTeaTime\Forum\Models;

use Illuminate\Support\Facades\Gate;
use Kalnoy\Nestedset\NodeTrait;
use TeamTeaTime\Forum\Support\Traits\CachesData;

class Category extends BaseModel
{
    use CachesData, NodeTrait;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
    protected $table = 'forum_categories';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
    public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
    protected $fillable = ['title', 'description', 'accepts_threads', 'is_private', 'color', 'thread_count', 'post_count'];

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

    /**
     * Relationship: Threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        $withTrashed = Gate::allows('viewTrashedThreads');
        $query = $this->hasMany(Thread::class);
        return $withTrashed ? $query->withTrashed() : $query;
    }

    /**
     * Attribute: Paginated threads.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getThreadsPaginatedAttribute()
    {
        return $this->threads()->orderBy('pinned', 'desc')->orderBy('updated_at', 'desc')
            ->paginate(config('forum.general.pagination.threads'));
    }

    /**
     * Attribute: Newest thread.
     *
     * @return Thread
     */
    public function getNewestThreadAttribute()
    {
        return $this->threads()->orderBy('created_at', 'desc')->first();
    }

    /**
     * Attribute: Latest active thread.
     *
     * @return Thread
     */
    public function getLatestActiveThreadAttribute()
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
