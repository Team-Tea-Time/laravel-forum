<?php namespace Eorzea\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Eorzea\Forum\AccessControl;

use Str;
use Config;

class Post extends AbstractBaseModel {

	use SoftDeletingTrait;

	protected $table      = 'forum_posts';
	public    $timestamps = true;
	protected $dates      = ['deleted_at'];
	protected $appends    = array('URL', 'postAlias', 'canPost');
	protected $guarded    = array('id');

	public function thread()
	{
		return $this->belongsTo('\Eorzea\Forum\Models\Thread', 'parent_thread');
	}

	public function author()
	{
		return $this->belongsTo(Config::get('forum::integration.user_model'), 'author_id');
	}

	public function scopeWhereThreadIn($query, Array $threads)
	{
		if (count($threads) == 0)
		{
			return $query;
		}

		return $query->whereIn('parent_thread', $threads);
	}

	public function getURLAttribute()
	{
		//TODO add page get parameter
		return $this->thread->URL;
	}

	public function getPostAliasAttribute()
	{
		$thread = $this->thread;
		$category = $thread->category;

		return route('forum.post.edit.post',
			array(
				'categoryID'		=> $category->id,
				'categoryAlias'	=> Str::slug($category->title, '-'),
				'threadID'			=> $thread->id,
				'threadAlias'		=> Str::slug($thread->title, '-'),
				'postID'				=> $this->id
			)
		);
	}

	public function getCanPostAttribute()
	{
		return AccessControl::check($this, 'edit_post');
	}

}
