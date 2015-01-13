<?php namespace Eorzea\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Eorzea\Forum\AccessControl;

use Str;
use Config;

class Thread extends AbstractBaseModel {

	use SoftDeletingTrait;

	protected $table      = 'forum_threads';
	public    $timestamps = true;
	protected $dates      = ['deleted_at'];
	protected $appends    = array('replyCount', 'URL', 'postAlias', 'canPost');
	protected $guarded    = array('id');

	public function category()
	{
		return $this->belongsTo('\Eorzea\Forum\Models\Category', 'parent_category');
	}

	public function author()
	{
		return $this->belongsTo(Config::get('forum::integration.user_model'), 'author_id');
	}

	public function posts()
	{
		return $this->hasMany('\Eorzea\Forum\Models\Post', 'parent_thread');
	}

	public function getReplyCountAttribute()
	{
		return $this->posts()->count();
	}

	public function getLastPageAttribute()
	{
		return $this->posts()->paginate(Config::get('forum::integration.posts_per_thread'))->getLastPage();
	}

	public function getLastPostAttribute()
	{
		return $this->posts()->orderBy('created_at', 'desc')->first();
	}

	public function getURLAttribute()
	{
		return route('forum.get.thread',
			array(
				'categoryID'		=> $this->category->id,
				'categoryAlias'	=> Str::slug($this->category->title, '-'),
				'threadID'			=> $this->id,
				'threadAlias'		=> Str::slug($this->title, '-'),
			)
		);
	}

	public function getPostAliasAttribute()
	{
		return route('forum.post.create.post',
			array(
				'categoryID'		=> $this->category->id,
				'categoryAlias'	=> Str::slug($this->category->title, '-'),
				'threadID'			=> $this->id,
				'threadAlias'		=> Str::slug($this->title, '-'),
			)
		);
	}

	public function getCanPostAttribute()
	{
		return AccessControl::check($this, 'create_posts');
	}

}
