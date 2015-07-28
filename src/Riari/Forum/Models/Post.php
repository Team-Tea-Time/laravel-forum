<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Riari\Forum\Models\Traits\HasAuthor;

class Post extends BaseModel
{
    use SoftDeletes, HasAuthor;

    // Eloquent properties
    protected $table        = 'forum_posts';
    protected $fillable     = ['thread_id', 'author_id', 'content'];
    public    $timestamps   = true;
    protected $dates        = ['deleted_at'];
    protected $with         = ['author'];
    protected $guarded      = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function thread()
    {
        return $this->belongsTo('\Riari\Forum\Models\Thread');
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    // Route attributes

    public function getRouteAttribute()
    {
        $perPage = config('forum.preferences.pagination.threads');
        $count = $this->thread->posts()->where('id', '<=', $this->id)->paginate($perPage)->total();
        $page = ceil($count / $perPage);

        return "{$this->thread->route}?page={$page}#post-{$this->id}";
    }

    public function getEditRouteAttribute()
    {
        return $this->getRoute('forum.post.edit');
    }

    public function getDeleteRouteAttribute()
    {
        return $this->getRoute('forum.post.delete');
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
