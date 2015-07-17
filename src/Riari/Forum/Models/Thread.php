<?php namespace Riari\Forum\Models;

use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Riari\Forum\Libraries\AccessControl;
use Riari\Forum\Libraries\Alerts;
use Riari\Forum\Libraries\Utils;
use Riari\Forum\Models\Traits\HasAuthor;

class Thread extends BaseModel
{
    use SoftDeletes, HasAuthor;

    // Eloquent properties
    protected $table         = 'forum_threads';
    public    $timestamps    = true;
    protected $dates         = ['deleted_at'];
    protected $appends       = ['lastPage', 'lastPost', 'lastPostRoute', 'route', 'lockRoute', 'pinRoute', 'replyRoute', 'deleteRoute'];
    protected $guarded       = ['id'];
    protected $with          = ['readers'];

    // Thread constants
    const     STATUS_UNREAD  = 'unread';
    const     STATUS_UPDATED = 'updated';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo('\Riari\Forum\Models\Category');
    }

    public function readers()
    {
        return $this->belongsToMany(config('forum.integration.user_model'), 'forum_threads_read', 'thread_id', 'user_id')->withTimestamps();
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
        $cutoff = config('forum.preferences.thread.cutoff_age');
        return $query->where('updated_at', '>', date('Y-m-d H:i:s', strtotime($cutoff)));
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    // Route attributes

    public function getRouteAttribute()
    {
        return $this->getRoute('forum.thread.show');
    }

    public function getReplyRouteAttribute()
    {
        return $this->getRoute('forum.post.create');
    }

    public function getPinRouteAttribute()
    {
        return $this->getRoute('forum.thread.pin');
    }

    public function getLockRouteAttribute()
    {
        return $this->getRoute('forum.thread.lock');
    }

    public function getDeleteRouteAttribute()
    {
        return $this->getRoute('forum.thread.delete');
    }

    public function getLastPostRouteAttribute()
    {
        return "{$this->route}?page={$this->lastPage}#post-{$this->lastPost->id}";
    }

    // General attributes

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
        $cutoff = config('forum.preferences.thread.cutoff_age');
        return (!$cutoff || $this->updated_at->timestamp < strtotime($cutoff));
    }

    // Current user: reader attributes

    public function getReaderAttribute()
    {
        if (!is_null(Utils::getCurrentUser())) {
            $reader = $this->readers()->where('user_id', '=', Utils::getCurrentUser()->id)->first();

            return (!is_null($reader)) ? $reader->pivot : null;
        }

        return null;
    }

    public function getUserReadStatusAttribute()
    {
        if (!$this->old && !is_null(Utils::getCurrentUser())) {
            if (is_null($this->reader)) {
                return self::STATUS_UNREAD;
            }

            return ($this->updatedSince($this->reader)) ? self::STATUS_UPDATED : false;
        }

        return false;
    }

    // Current user: permission attributes

    public function getUserCanReplyAttribute()
    {
        return AccessControl::check($this, 'reply_to_thread', false);
    }

    public function getCanReplyAttribute()
    {
        return $this->userCanReply;
    }

    public function getUserCanPinAttribute()
    {
        return AccessControl::check($this, 'pin_threads', false);
    }

    public function getCanPinAttribute()
    {
        return $this->userCanPin;
    }

    public function getUserCanLockAttribute()
    {
        return AccessControl::check($this, 'lock_threads', false);
    }

    public function getCanLockAttribute()
    {
        return $this->userCanLock;
    }

    public function getUserCanDeleteAttribute()
    {
        return AccessControl::check($this, 'delete_threads', false);
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
            'categoryID'    => $this->category->id,
            'categoryAlias' => Str::slug($this->category->title, '-'),
            'threadID'      => $this->id,
            'threadAlias'   => Str::slug($this->title, '-')
        );

        return $components;
    }

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

    public function toggle($property)
    {
        parent::toggle($property);

        Alerts::add('success', trans('forum::threads.updated'));
    }
}
