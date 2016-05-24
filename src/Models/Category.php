<?php namespace Riari\Forum\Models;

use Illuminate\Support\Facades\Gate;
use Riari\Forum\Support\Traits\CachesData;

class Category extends BaseModel
{
    use CachesData;

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
    protected $fillable = ['category_id', 'title', 'description', 'weight', 'enable_threads', 'private'];

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
     * Relationship: Parent category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_id')->orderBy('weight');
    }

    /**
     * Relationship: Child categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'category_id')->orderBy('weight');
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
     * Attribute: Child categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getChildrenAttribute()
    {
        $children = $this->children()->get();

        $children = $children->filter(function ($category) {
            if ($category->private) {
                return Gate::allows('view', $category);
            }

            return true;
        });

        return $children;
    }

    /**
     * Attribute: Paginated threads.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getThreadsPaginatedAttribute()
    {
        return $this->threads()->orderBy('pinned', 'desc')->orderBy('updated_at', 'desc')
            ->paginate(config('forum.preferences.pagination.threads'));
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

    /**
     * Attribute: New threads enabled.
     *
     * @return bool
     */
    public function getThreadsEnabledAttribute()
    {
        return $this->enable_threads;
    }

    /**
     * Attribute: Thread count.
     *
     * @return int
     */
    public function getThreadCountAttribute()
    {
        return $this->remember('threadCount', function () {
            return $this->threads->count();
        });
    }

    /**
     * Attribute: Post (reply) count.
     *
     * @return int
     */
    public function getPostCountAttribute()
    {
        return $this->remember('postCount', function () {
            $replyCount = 0;

            $threads = $this->threads()->get(['id']);

            foreach ($threads as $thread) {
                $replyCount += $thread->posts->count() - 1;
            }

            return $replyCount;
        });
    }

    /**
     * Attribute: Deepest child.
     *
     * @return Category
     */
    public function getDeepestChildAttribute()
    {
        $category = $this;

        return $this->remember('deepestChild', function () use ($category) {
            while ($category->parent) {
                $category = $category->parent;
            }

            return $category;
        });
    }

    /**
     * Attribute: Depth.
     *
     * @return int
     */
    public function getDepthAttribute()
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
