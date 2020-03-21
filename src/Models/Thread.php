<?php

namespace TeamTeaTime\Forum\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Traits\HasAuthor;
use TeamTeaTime\Forum\Support\Traits\CachesData;

class Thread extends BaseModel
{
    use SoftDeletes, HasAuthor, CachesData;

    protected $table = 'forum_threads';
    protected $dates = ['deleted_at'];
    protected $fillable = ['category_id', 'author_id', 'title', 'locked', 'pinned', 'reply_count'];

    const STATUS_UNREAD = 'unread';
    const STATUS_UPDATED = 'updated';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('forum.general.pagination.threads');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(
            config('forum.integration.user_model'),
            'forum_threads_read',
            'thread_id',
            'user_id'
        )->withTimestamps();
    }

    public function posts(): HasMany
    {
        $withTrashed = config('forum.general.display_trashed_posts') || Gate::allows('viewTrashedPosts');
        $query = $this->hasMany(Post::class);
        return $withTrashed ? $query->withTrashed() : $query;
    }

    public function scopeRecent(Builder $query): Builder
    {
        $age = strtotime(config('forum.general.old_thread_threshold'), 0);
        $cutoff = time() - $age;

        return $query->where('updated_at', '>', date('Y-m-d H:i:s', $cutoff))->orderBy('updated_at', 'desc');
    }

    public function getPostsPaginatedAttribute(): LengthAwarePaginator
    {
        return $this->posts()->paginate();
    }

    public function getLastPageAttribute(): int
    {
        return $this->postsPaginated->lastPage();
    }

    public function getFirstPostAttribute(): Post
    {
        return $this->posts()->orderBy('created_at', 'asc')->first();
    }

    public function getLastPostAttribute(): Post
    {
        return $this->posts()->orderBy('created_at', 'desc')->first();
    }

    public function getLastPostTimeAttribute(): Carbon
    {
        return $this->lastPost->created_at;
    }

    public function getOldAttribute(): bool
    {
        $age = config('forum.preferences.old_thread_threshold');
        return (!$age || $this->updated_at->timestamp < (time() - strtotime($age, 0)));
    }

    public function getReaderAttribute()
    {
        if (! auth()->check()) return null;

        $reader = $this->readers()->where('forum_threads_read.user_id', auth()->user()->getKey())->first();

        return (! is_null($reader)) ? $reader->pivot : null;
    }

    public function getUserReadStatusAttribute(): ?string
    {
        if ($this->old || ! auth()->check()) return false;

        if (is_null($this->reader)) return self::STATUS_UNREAD;

        return ($this->updatedSince($this->reader)) ? self::STATUS_UPDATED : false;
    }

    public function markAsRead(int $userID): Thread
    {
        if ($this->old) return false;

        if (is_null($this->reader))
        {
            $this->readers()->attach($userID);
        }
        elseif ($this->updatedSince($this->reader))
        {
            $this->reader->touch();
        }
    }
}
