<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Riari\Forum\Models\Traits\HasAuthor;

class Post extends BaseModel
{
    use SoftDeletes, HasAuthor;

    // Eloquent properties
    protected $table      = 'forum_posts';
    public    $timestamps = true;
    protected $dates      = ['deleted_at'];
    protected $appends    = ['route', 'editRoute', 'deleteRoute'];
    protected $with       = ['author'];
    protected $guarded    = ['id'];

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

    protected function getAccessParams()
    {
        $parameters = [
            'category'  => $this->thread->category,
            'thread'    => $this->thread,
            'post'      => $this
        ];

        return $parameters;
    }

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
}
