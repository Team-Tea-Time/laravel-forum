<?php

namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Riari\Forum\Models\Traits\HasAuthor;

class Post extends BaseModel
{
    use SoftDeletes, HasAuthor;

    // Eloquent properties
    protected $table        = 'forum_posts';
    protected $fillable     = ['thread_id', 'author_id', 'post_id', 'content'];
    public    $timestamps   = true;
    protected $with         = ['author'];
    protected $guarded      = ['id'];

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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function thread()
    {
        return $this->belongsTo('\Riari\Forum\Models\Thread')->withTrashed();
    }

    public function parent()
    {
        return $this->belongsTo('\Riari\Forum\Models\Post', 'post_id')->withTrashed();
    }

    public function children()
    {
        return $this->hasMany('\Riari\Forum\Models\Post', 'post_id')->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    // Route attributes

    public function getUrlAttribute()
    {
        $perPage = config('forum.preferences.pagination.threads');
        $count = $this->thread->posts()->where('id', '<=', $this->id)->paginate($perPage)->total();
        $page = ceil($count / $perPage);

        return "{$this->thread->route}?page={$page}#post-{$this->id}";
    }

    public function getRouteAttribute()
    {
        return $this->buildRoute('forum.post.show');
    }

    public function getEditRouteAttribute()
    {
        return $this->buildRoute('forum.post.edit');
    }

    public function getDeleteRouteAttribute()
    {
        return $this->buildRoute('forum.api.post.destroy');
    }

    public function getReplyRouteAttribute()
    {
        return $this->buildRoute('forum.post.create', ['post_id' => $this->id]);
    }

    /**
     * Helper: Get route parameters.
     *
     * @return array
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
