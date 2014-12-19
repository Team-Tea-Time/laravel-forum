<?php namespace Eorzea\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ForumThread extends AbstractForumBaseModel
{

	use SoftDeletingTrait;

	protected $table      = 'forum_threads';
	public    $timestamps = true;
	protected $dates      = ['deleted_at'];
	protected $appends    = array('replyCount', 'url', 'postAlias', 'canPost');
	protected $guarded    = array('id');

	public function category()
	{
		return $this->belongsTo('\Eorzea\Forum\Models\ForumCategory', 'parent_category');
	}

	public function author()
	{
		return $this->belongsTo(Config::get('forum::integration.usermodel'), 'author_id');
	}

	public function posts()
	{
		return $this->hasMany('\Eorzea\Forum\Models\ForumPost', 'parent_thread');
	}

	public function getReplyCountAttribute()
	{
		return $this->posts()->count();
	}

	public function getAliasAttribute()
	{
		return action(Config::get('forum::integration.viewcontroller').'@getThread',
			array(
				'categoryID'  => $this->category->id,
				'categoryAlias' => Str::slug($this->category->title, '_'),
				'threadID'     => $this->id,
				'threadAlias'    => Str::slug($this->title, '_'),
			)
		);
	}

	public function getPostAliasAttribute()
	{
		return action(\Config::get('forum::integration.postcontroller').'@postNewPost',
			array(
				'categoryID'  => $this->category->id,
				'categoryAlias' => \Str::slug($this->category->title, '_'),
				'threadID'     => $this->id,
				'threadAlias'    => \Str::slug($this->title, '_'),
			)
		);
	}

	public function getCanPostAttribute()
	{
		return $this->computeCanPostAttribute('rights.postthread');
	}

}
