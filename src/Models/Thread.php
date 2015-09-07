<?php

namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Riari\Forum\Models\Traits\HasAuthor;
use Riari\Forum\Models\Traits\HasSlug;

class Thread extends BaseModel
{
    use SoftDeletes, HasAuthor, HasSlug;

    /**
     * Eloquent attributes
     */
    protected $table            = 'forum_threads';
    protected $fillable         = ['category_id', 'author_id', 'title', 'locked', 'pinned'];
    public    $timestamps       = true;
    protected $with             = ['author'];
    protected $guarded          = ['id'];

    /**
     * Constants
     */
    const     STATUS_UNREAD     = 'unread';
    const     STATUS_UPDATED    = 'updated';

    /**
     * Create a new thread model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('forum.preferences.pagination.threads');
    }

    /**
     * Relationship: Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('\Riari\Forum\Models\Category');
    }

    /**
     * Relationship: Readers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readers()
    {
        return $this->belongsToMany(
                config('forum.integration.user_model'),
                'forum_threads_read',
                'thread_id',
                'user_id'
            )->withTimestamps();
    }

    /**
     * Relationship: Posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany('\Riari\Forum\Models\Post');
    }

    /**
     * Relationship: Posts (including soft-deleted).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function postsWithTrashed()
    {
        return $this->posts()->withTrashed();
    }

    /**
     * Scope: Recent threads.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeRecent($query)
    {
        $cutoff = config('forum.preferences.old_thread_threshold');
        return $query->where('updated_at', '>', date('Y-m-d H:i:s', strtotime($cutoff)))
            ->orderBy('updated_at', 'desc');
    }

    /**
     * Attribute: Thread route.
     *
     * @return string
     */
    public function getRouteAttribute()
    {
        return $this->buildRoute('forum.thread.show');
    }

    /**
     * Attribute: Reply route.
     *
     * @return string
     */
    public function getReplyRouteAttribute()
    {
        return $this->buildRoute('forum.post.create');
    }

    /**
     * Attribute: Update route.
     *
     * @return string
     */
    public function getUpdateRouteAttribute()
    {
        return $this->buildRoute('forum.api.thread.update');
    }

    /**
     * Attribute: Delete route.
     *
     * @return string
     */
    public function getDeleteRouteAttribute()
    {
        return $this->buildRoute('forum.api.thread.destroy');
    }

    /**
     * Attribute: Restore route.
     *
     * @return string
     */
    public function getRestoreRouteAttribute()
    {
        return $this->buildRoute('forum.api.thread.restore');
    }

    /**
     * Attribute: Force delete route.
     *
     * @return string
     */
    public function getForceDeleteRouteAttribute()
    {
        return $this->buildRoute('forum.api.thread.destroy', ['force' => 1]);
    }

    /**
     * Attribute: Last post URL.
     *
     * @return string
     */
    public function getLastPostUrlAttribute()
    {
        return "{$this->route}?page={$this->lastPage}#post-{$this->lastPost->id}";
    }

    /**
     * Attribute: Paginated posts.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPostsPaginatedAttribute()
    {
        return $this->posts()->paginate(config('forum.preferences.pagination.posts'));
    }

    /**
     * Attribute: Paginated posts (including soft-deleted).
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPostsWithTrashedPaginatedAttribute()
    {
        return $this->posts()->withTrashed()->paginate(config('forum.preferences.pagination.posts'));
    }

    /**
     * Attribute: Pagination links.
     *
     * @return string
     */
    public function getPageLinksAttribute()
    {
        return $this->postsPaginated->render();
    }

    /**
     * Attribute: The last page number of the thread.
     *
     * @return int
     */
    public function getLastPageAttribute()
    {
        return $this->postsPaginated->lastPage();
    }

    /**
     * Attribute: The last post in the thread.
     *
     * @return Post
     */
    public function getLastPostAttribute()
    {
        return $this->posts()->orderBy('created_at', 'desc')->first();
    }

    /**
     * Attribute: Creation time of the last post in the thread.
     *
     * @return \Carbon\Carbon
     */
    public function getLastPostTimeAttribute()
    {
        return $this->lastPost->created_at;
    }

    /**
     * Attribute: Number of thread replies.
     *
     * @return int
     */
    public function getReplyCountAttribute()
    {
        return ($this->posts->count() - 1);
    }

    /**
     * Attribute: 'Old' flag.
     *
     * @return boolean
     */
    public function getOldAttribute()
    {
        $cutoff = config('forum.preferences.old_thread_threshold');
        return (!$cutoff || $this->updated_at->timestamp < strtotime($cutoff));
    }

    /**
     * Attribute: Currently authenticated reader.
     *
     * @return mixed
     */
    public function getReaderAttribute()
    {
        if (auth()->check()) {
            $reader = $this->readers()->where('user_id', auth()->user()->id)->first();

            return (!is_null($reader)) ? $reader->pivot : null;
        }

        return null;
    }

    /**
     * Attribute: Read/unread/updated status for current reader.
     *
     * @return mixed
     */
    public function getUserReadStatusAttribute()
    {
        if (!$this->old && auth()->check()) {
            if (is_null($this->reader)) {
                return self::STATUS_UNREAD;
            }

            return ($this->updatedSince($this->reader)) ? self::STATUS_UPDATED : false;
        }

        return false;
    }

    /**
     * Attribute: Current reader's new/updated threads.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNewForReaderAttribute()
    {
        $threads = $this->recent();

        // If the user is logged in, filter the threads according to read status
        if (auth()->check()) {
            $threads = $threads->filter(function ($thread)
            {
                return $thread->userReadStatus;
            });
        }

        // Filter the threads according to the user's permissions
        $threads = $threads->filter(function ($thread)
        {
            return $thread->category->userCanView;
        });

        return $threads;
    }

    /**
     * Helper: Get route parameters.
     *
     * @return array
     */
    public function getRouteParameters()
    {
        return [
            'category'      => $this->category->id,
            'category_slug' => $this->category->slug,
            'thread'        => $this->id,
            'thread_slug'   => $this->slug
        ];
    }

    /**
     * Helper: Mark this thread as read for the given user ID.
     *
     * @param  int  $userID
     * @return void
     */
    public function markAsRead($userID)
    {
        if (!$this->old) {
            if (is_null($this->reader)) {
                $this->readers()->attach($userID);
            } elseif ($this->updatedSince($this->reader)) {
                $this->reader->touch();
            }
        }
    }
}
