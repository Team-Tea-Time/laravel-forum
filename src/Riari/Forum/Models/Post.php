<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Riari\Forum\Models\Traits\HasAuthor;

class Post extends BaseModel
{
    use SoftDeletes, HasAuthor;

    // Eloquent properties
    protected $table        = 'forum_posts';
    protected $fillable     = ['thread_id', 'author_id', 'post_id', 'content'];
    public    $timestamps   = true;
    protected $dates        = ['deleted_at'];
    protected $with         = ['author'];
    protected $guarded      = ['id'];

    /**
     * Create a new post model instance.
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
        return $this->belongsTo('\Riari\Forum\Models\Thread');
    }

    public function parent()
    {
        return $this->belongsTo('\Riari\Forum\Models\Post', 'post_id');
    }

    public function children()
    {
        return $this->hasMany('\Riari\Forum\Models\Post', 'post_id');
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
        return $this->getRoute('forum.post.show');
    }

    public function getEditRouteAttribute()
    {
        return $this->getRoute('forum.post.edit');
    }

    public function getDeleteRouteAttribute()
    {
        return $this->getRoute('forum.post.delete');
    }

    public function getReplyRouteAttribute()
    {
        return $this->thread->getRoute('forum.post.create', ['post_id' => $this->id]);
    }

    // Current user: permission attributes

    public function getUserCanEditAttribute()
    {
        return $this->userCan('forum.post.edit');
    }

    public function getUserCanDeleteAttribute()
    {
        return $this->userCan('forum.post.delete');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Return an array of components used to construct this model's route.
     *
     * @return array
     */
    protected function getRouteComponents()
    {
        $components = [
            'category'      => $this->thread->category->id,
            'categoryAlias' => Str::slug($this->thread->category->title, '-'),
            'thread'        => $this->thread->id,
            'threadAlias'   => Str::slug($this->thread->title, '-'),
            'post'          => $this->id
        ];

        return $components;
    }

    /**
     * Return an array of parameters used by the userCan() method to check
     * permissions.
     *
     * @return array
     */
    protected function getAccessParams()
    {
        $parameters = [
            'category'  => $this->thread->category,
            'thread'    => $this->thread,
            'post'      => $this
        ];

        return $parameters;
    }
}
