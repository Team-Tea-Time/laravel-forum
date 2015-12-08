<?php

namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Riari\Forum\Models\Traits\HasAuthor;

class Post extends BaseModel
{
    use SoftDeletes, HasAuthor;

    /**
     * Eloquent attributes
     */
    protected $table        = 'forum_posts';
    public    $timestamps   = true;
    protected $fillable     = ['thread_id', 'author_id', 'post_id', 'content'];
    protected $guarded      = ['id'];
    protected $with         = ['author'];

    /**
     * Create a new post model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setPerPage(config('forum.preferences.pagination.posts'));
    }

    /**
     * Relationship: Thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class)->withTrashed();
    }

    /**
     * Relationship: Parent post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Relationship: Child posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Post::class, 'post_id')->withTrashed();
    }

    /**
     * Attribute: URL.
     *
     * @return string
     *
     * @deprecated as of 3.0.2
     * @todo remove before 3.1.0
     */
    public function getUrlAttribute()
    {
        $perPage = config('forum.preferences.pagination.threads');
        $count = $this->thread->posts()->where('id', '<=', $this->id)->paginate($perPage)->total();
        $page = ceil($count / $perPage);

        return "{$this->thread->route}?page={$page}#post-{$this->id}";
    }

    /**
     * Attribute: Route.
     *
     * @return string
     *
     * @deprecated as of 3.0.2
     * @todo remove before 3.1.0
     */
    public function getRouteAttribute()
    {
        return $this->buildRoute('forum.post.show');
    }

    /**
     * Attribute: Edit route.
     *
     * @return string
     *
     * @deprecated as of 3.0.2
     * @todo remove before 3.1.0
     */
    public function getEditRouteAttribute()
    {
        return $this->buildRoute('forum.post.edit');
    }

    /**
     * Attribute: Delete route.
     *
     * @return string
     *
     * @deprecated as of 3.0.2
     * @todo remove before 3.1.0
     */
    public function getDeleteRouteAttribute()
    {
        return $this->buildRoute('forum.api.post.destroy');
    }

    /**
     * Attribute: Reply route.
     *
     * @return string
     *
     * @deprecated as of 3.0.2
     * @todo remove before 3.1.0
     */
    public function getReplyRouteAttribute()
    {
        return $this->buildRoute('forum.post.create', ['post_id' => $this->id]);
    }

    /**
     * Attribute: First post flag.
     *
     * @return boolean
     */
    public function getIsFirstAttribute()
    {
        return $this->id == $this->thread->firstPost->id;
    }

    /**
     * Helper: Get route parameters.
     *
     * @return array
     *
     * @deprecated as of 3.0.2
     * @todo remove before 3.1.0
     */
    protected function getRouteParameters()
    {
        return [
            'category'      => $this->thread->category->id,
            'category_slug' => $this->thread->category->slug,
            'thread'        => $this->thread->id,
            'thread_slug'   => $this->thread->slug,
            'post'          => $this->id
        ];
    }
}
