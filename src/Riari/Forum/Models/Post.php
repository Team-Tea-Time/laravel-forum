<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Riari\Forum\Libraries\AccessControl;

use Config;
use Str;

class Post extends BaseModel {

	use SoftDeletingTrait;

	protected $table      = 'forum_posts';
	public    $timestamps = true;
	protected $dates      = ['deleted_at'];
	protected $appends    = ['Route', 'editRoute'];
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

	public function getRouteAttribute()
	{
		return $this->thread->Route;
	}

	protected function getRouteComponents()
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

	public function getEditRouteAttribute()
	{
		return $this->getRoute('forum.get.edit.post');
	}

	public function getDeleteRouteAttribute()
	{
		return $this->getRoute('forum.get.delete.post');
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
