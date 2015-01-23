<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Riari\Forum\Libraries\AccessControl;

use Config;
use Str;

class Thread extends BaseModel {

	use SoftDeletingTrait;

	protected $table      = 'forum_threads';
	public    $timestamps = true;
	protected $dates      = ['deleted_at'];
	protected $appends    = ['lastPage', 'lastPost', 'lastPostURL', 'URL', 'replyURL', 'deleteURL'];
	protected $guarded    = ['id'];

	public function category()
	{
		return $this->belongsTo('\Riari\Forum\Models\Category', 'parent_category');
	}

	public function author()
	{
		return $this->belongsTo(Config::get('forum::integration.user_model'), 'author_id');
	}

	public function posts()
	{
		return $this->hasMany('\Riari\Forum\Models\Post', 'parent_thread')->orderBy('created_at', 'desc');
	}

	public function getLastPageAttribute()
	{
		return $this->posts()->paginate(Config::get('forum::integration.posts_per_thread'))->getLastPage();
	}

	public function getLastPostAttribute()
	{
		return $this->posts->first();
	}

	public function getLastPostURLAttribute()
	{
		return $this->URL . '?page=' . $this->lastPage . '#post-' . $this->lastPost->id;
	}

	public function getLastPostTimeAttribute()
	{
		return $this->lastPost->created_at;
	}

	protected function getURLComponents()
	{
		$components = array(
			'categoryID'		=> $this->category->id,
			'categoryAlias'	=> Str::slug($this->category->title, '-'),
			'threadID'			=> $this->id,
			'threadAlias'		=> Str::slug($this->title, '-')
		);

		return $components;
	}

	public function getURLAttribute()
	{
		return $this->getRoute('forum.get.view.thread');
	}

	public function getReplyURLAttribute()
	{
		return $this->getRoute('forum.get.reply.thread');
	}

	public function getPinURLAttribute()
	{
		return $this->getRoute('forum.get.pin.thread');
	}

	public function getLockURLAttribute()
	{
		return $this->getRoute('forum.get.lock.thread');
	}

	public function getDeleteURLAttribute()
	{
		return $this->getRoute('forum.get.delete.thread');
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

}
