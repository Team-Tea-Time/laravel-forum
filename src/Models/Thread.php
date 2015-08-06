<?php

namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Riari\Forum\Models\Traits\HasAuthor;
use Riari\Forum\Models\Traits\HasSlug;

class Thread extends BaseModel
{
    use SoftDeletes, HasAuthor, HasSlug;

    // Eloquent properties
    protected $table            = 'forum_threads';
    protected $fillable         = ['category_id', 'author_id', 'title', 'locked', 'pinned'];
    public    $timestamps       = true;
    protected $with             = ['author'];
    protected $guarded          = ['id'];

    // Thread constants
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
        $this->routeParameters = [
            'category'      => $this->category->id,
            'category_slug' => $this->category->slug,
            'thread'        => $this->id,
            'thread_slug'   => $this->slug
        ];
    }

    public function category()
    {
        return $this->belongsTo('\Riari\Forum\Models\Category');
    }

    public function readers()
    {
        return $this->belongsToMany(
                config('forum.integration.user_model'),
                'forum_threads_read',
                'thread_id',
                'user_id'
            )->withTimestamps();
    }

    public function posts()
    {
        return $this->hasMany('\Riari\Forum\Models\Post');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeRecent($query)
    {
        $cutoff = config('forum.preferences.old_thread_threshold');
        return $query->where('updated_at', '>', date('Y-m-d H:i:s', strtotime($cutoff)))
            ->orderBy('updated_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    // Route attributes

    public function getRouteAttribute()
    {
        return $this->buildRoute('forum.thread.show');
    }

    public function getReplyRouteAttribute()
    {
        return $this->buildRoute('forum.post.create');
    }

    public function getUpdateRouteAttribute()
    {
        return $this->buildRoute('forum.api.thread.update');
    }

    public function getDeleteRouteAttribute()
    {
        return $this->buildRoute('forum.api.thread.destroy');
    }

    public function getRestoreRouteAttribute()
    {
        return $this->buildRoute('forum.api.thread.restore');
    }

    public function getForceDeleteRouteAttribute()
    {
        return $this->buildRoute('forum.api.thread.destroy', ['force' => 1]);
    }

    public function getLastPostUrlAttribute()
    {
        return "{$this->route}?page={$this->lastPage}#post-{$this->lastPost->id}";
    }

    // General attributes

    public function getPostsPaginatedAttribute()
    {
        return $this->posts()->paginate(config('forum.preferences.pagination.posts'));
    }

    public function getPageLinksAttribute()
    {
        return $this->postsPaginated->render();
    }

    public function getLastPageAttribute()
    {
        return $this->postsPaginated->lastPage();
    }

    public function getLastPostAttribute()
    {
        return $this->posts()->orderBy('created_at', 'desc')->first();
    }

    public function getLastPostTimeAttribute()
    {
        return $this->lastPost->created_at;
    }

    public function getReplyCountAttribute()
    {
        return ($this->posts->count() - 1);
    }

    public function getOldAttribute()
    {
        $cutoff = config('forum.preferences.old_thread_threshold');
        return (!$cutoff || $this->updated_at->timestamp < strtotime($cutoff));
    }

    // Current user: reader attributes

    public function getReaderAttribute()
    {
        if (auth()->check()) {
            $reader = $this->readers()->where('user_id', auth()->user()->id)->first();

            return (!is_null($reader)) ? $reader->pivot : null;
        }

        return null;
    }

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

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

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
