<?php namespace Riari\Forum\Models;

use Riari\Forum\Libraries\AccessControl;
use Riari\Forum\Libraries\Alerts;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use Redirect;

class Thread extends BaseModel {

    use SoftDeletes;

    protected $table      = 'forum_threads';
    public    $timestamps = true;
    protected $dates      = ['deleted_at'];
    protected $appends    = ['lastPage', 'lastPost', 'lastPostRoute', 'route', 'lockRoute', 'pinRoute', 'replyRoute', 'deleteRoute'];
    protected $guarded    = ['id'];

    public function category()
    {
        return $this->belongsTo('\Riari\Forum\Models\Category', 'parent_category');
    }

    public function author()
    {
        return $this->belongsTo(config('forum.integration.user_model'), 'author_id');
    }

    public function posts()
    {
        return $this->hasMany('\Riari\Forum\Models\Post', 'parent_thread');
    }

    public function getPostsPaginatedAttribute()
    {
        return $this->posts()->paginate(config('forum.preferences.posts_per_thread'));
    }

    public function getPageLinksAttribute()
    {
        return $this->postsPaginated->render();
    }

    public function getLastPageAttribute()
    {
        return $this->postsPaginated->getLastPage();
    }

    public function getLastPostAttribute()
    {
        return $this->posts()->orderBy('created_at', 'desc')->first();
    }

    public function getLastPostRouteAttribute()
    {
        return $this->Route . '?page=' . $this->lastPage . '#post-' . $this->lastPost->id;
    }

    public function getLastPostTimeAttribute()
    {
        return $this->lastPost->created_at;
    }

    protected function getRouteComponents()
    {
        $components = array(
            'categoryID' => $this->category->id,
            'categoryAlias' => Str::slug($this->category->title, '-'),
            'threadID' => $this->id,
            'threadAlias' => Str::slug($this->title, '-')
        );

        return $components;
    }

    public function getRouteAttribute()
    {
        return $this->getRoute('forum.get.view.thread');
    }

    public function getReplyRouteAttribute()
    {
        return $this->getRoute('forum.get.reply.thread');
    }

    public function getPinRouteAttribute()
    {
        return $this->getRoute('forum.post.pin.thread');
    }

    public function getLockRouteAttribute()
    {
        return $this->getRoute('forum.post.lock.thread');
    }

    public function getDeleteRouteAttribute()
    {
        return $this->getRoute('forum.delete.thread');
    }

    public function getCanReplyAttribute()
    {
        return AccessControl::check($this, 'reply_to_thread', FALSE);
    }

    public function getCanPinAttribute()
    {
        return AccessControl::check($this, 'pin_threads', FALSE);
    }

    public function getCanLockAttribute()
    {
        return AccessControl::check($this, 'lock_threads', FALSE);
    }

    public function getCanDeleteAttribute()
    {
        return AccessControl::check($this, 'delete_threads', FALSE);
    }

    public function toggle($property)
    {
        parent::toggle($property);

        Alerts::add('success', trans('forum::base.thread_updated'));
    }

}
