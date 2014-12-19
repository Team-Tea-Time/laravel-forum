<?php namespace Eorzea\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ForumPost extends AbstractForumBaseModel {

	use SoftDeletingTrait;

	protected $table      = 'forum_posts';
	public    $timestamps = true;
	protected $dates      = ['deleted_at'];
	protected $appends    = array('url', 'postAlias', 'canPost');
	protected $guarded    = array('id');

	public function thread()
	{
		return $this->belongsTo('\Eorzea\Forum\Models\ForumThread', 'parent_thread');
	}

	public function author()
	{
		return $this->belongsTo(Config::get('forum::integration.usermodel'), 'author_id');
	}

	public function scopeWhereThreadIn($query, Array $threads)
	{
		if (count($threads) == 0)
		{
			return $query;
		}

		return $query->whereIn('parent_thread', $threads);
	}

	public function getAliasAttribute()
	{
		//TODO add page get parameter
		return $this->thread->url;
	}

	public function getPostAliasAttribute()
	{
		$thread    = $this->thread;
		$category = $thread->category;

		return action(Config::get('forum::integration.postcontroller').'@postEditPost',
			array(
				'categoryID'  => $category->id,
				'categoryAlias' => Str::slug($category->title, '_'),
				'threadID'     => $thread->id,
				'threadAlias'    => Str::slug($thread->title, '_'),
				'postID'   => $this->id
			)
		);
	}

}
