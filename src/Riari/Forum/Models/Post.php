<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Riari\Forum\AccessControl;

use Config;
use Str;

class Post extends BaseModel {

	use SoftDeletingTrait;

	protected $table      = 'forum_posts';
	public    $timestamps = true;
	protected $dates      = ['deleted_at'];
	protected $appends    = ['URL', 'editURL'];
	protected $with    		= ['author'];
	protected $guarded    = ['id'];

	public function thread()
	{
		return $this->belongsTo('\Riari\Forum\Models\Thread', 'parent_thread');
	}

	public function author()
	{
		return $this->belongsTo(Config::get('forum::integration.user_model'), 'author_id');
	}

	public function getURLAttribute()
	{
		return $this->thread->URL;
	}

	private function getURLComponents()
	{
		$components = array(
			'categoryID'		=> $this->thread->category->id,
			'categoryAlias'	=> Str::slug($this->thread->category->title, '-'),
			'threadID'			=> $this->thread->id,
			'threadAlias'		=> Str::slug($this->thread->title, '-'),
			'postID'				=> $this->id
		);

		return $components;
	}

	public function getEditURLAttribute()
	{
		return route('forum.get.edit.post', $this->getURLComponents());
	}

	public function getDeleteURLAttribute()
	{
		return route('forum.get.delete.post', $this->getURLComponents());
	}

	public function getCanPostAttribute()
	{
		return AccessControl::check($this, 'edit_post', FALSE);
	}

	public function getCanDeleteAttribute()
	{
		return AccessControl::check($this, 'delete_posts', FALSE);
	}

}
