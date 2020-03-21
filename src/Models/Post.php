<?php

namespace TeamTeaTime\Forum\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use TeamTeaTime\Forum\Models\Traits\HasAuthor;
use TeamTeaTime\Forum\Support\Traits\CachesData;

class Post extends BaseModel
{
    use SoftDeletes, HasAuthor, CachesData;

    protected $table = 'forum_posts';
    protected $dates = ['deleted_at'];
    protected $fillable = ['thread_id', 'author_id', 'post_id', 'content'];
    protected $appends = ['isFirst'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('forum.general.pagination.posts');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class)->withTrashed();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Post::class, 'post_id')->withTrashed();
    }

    public function getIsFirstAttribute(): bool
    {
        return $this->id == $this->thread->firstPost->id;
    }

    public function getSequenceNumber(): int
    {
        foreach ($this->thread->posts as $index => $post)
        {
            if ($post->id == $this->id) return $index + 1;
        }
    }
}
