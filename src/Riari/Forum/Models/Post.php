<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Riari\Forum\Libraries\AccessControl;
use Riari\Forum\Models\Traits\HasAuthor;
use Riari\Forum\Models\Traits\UsesBBCode;

class Post extends BaseModel {

    use SoftDeletes, HasAuthor, UsesBBCode;

    // Eloquent properties
    protected $table      = 'forum_posts';
    public    $timestamps = true;
    protected $dates      = ['deleted_at'];
    protected $appends    = ['route', 'editRoute'];
    protected $with       = ['author'];
    protected $guarded    = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function thread()
    {
        return $this->belongsTo('\Riari\Forum\Models\Thread', 'parent_thread');
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    // Route attributes

    public function getRouteAttribute()
    {
        $perPage = config('forum.preferences.posts_per_thread');
        $count = $this->thread->posts()->where('id', '<=', $this->id)->paginate($perPage)->total();
        $page = ceil($count / $perPage);

        return "{$this->thread->route}?page={$page}#post-{$this->id}";
    }

    public function getEditRouteAttribute()
    {
        return $this->getRoute('forum.get.edit.post');
    }

    public function getDeleteRouteAttribute()
    {
        return $this->getRoute('forum.get.delete.post');
    }

    // Current user: permission attributes

    public function getUserCanEditAttribute()
    {
        return AccessControl::check($this, 'edit_post', false);
    }

    public function getCanEditAttribute()
    {
        return $this->userCanEdit;
    }

    public function getUserCanDeleteAttribute()
    {
        return AccessControl::check($this, 'delete_posts', false);
    }

    public function getCanDeleteAttribute()
    {
        return $this->userCanDelete;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function getRouteComponents()
    {
        $components = array(
            'categoryID'    => $this->thread->category->id,
            'categoryAlias' => Str::slug($this->thread->category->title, '-'),
            'threadID'      => $this->thread->id,
            'threadAlias'   => Str::slug($this->thread->title, '-'),
            'postID'        => $this->id
        );

        return $components;
    }

}
