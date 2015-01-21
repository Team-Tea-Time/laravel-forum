<?php namespace Eorzea\Forum\Models;

use Eorzea\Forum\Models\Thread;
use Eorzea\Forum\AccessControl;

use Config;
use Str;

class Category extends AbstractBaseModel {

	protected $table      = 'forum_categories';
	public    $timestamps = false;
	protected $appends    = ['threadCount', 'replyCount', 'URL', 'newThreadURL'];

	public function parentCategory()
	{
		return $this->belongsTo('\Eorzea\Forum\Models\Category', 'parent_category')->orderBy('weight');
	}

	public function subcategories()
	{
		return $this->hasMany('\Eorzea\Forum\Models\Category', 'parent_category')->orderBy('weight');
	}

	public function threads()
	{
		return $this->hasMany('\Eorzea\Forum\Models\Thread', 'parent_category')->with('category', 'posts')->orderBy('pinned', 'desc')->orderBy('updated_at', 'desc');
	}

	public function scopeWhereTopLevel($query)
	{
		return $query->where('parent_category', '=', NULL);
	}

	public function getThreadCountAttribute()
	{
		return $this->rememberAttribute('threadCount', function(){
			return $this->threads->count();
		});
	}

	public function getReplyCountAttribute()
	{
		return $this->rememberAttribute('replyCount', function(){
			$replyCount = 0;

			$threads = $this->threads()->get(array('id'));

			foreach ($threads as $thread) {
				$replyCount += $thread->posts->count();
			}

			return $replyCount;
		});
	}

	private function getURLComponents()
	{
		$components = array(
			'categoryID'		=> $this->id,
			'categoryAlias'	=> Str::slug($this->title, '-')
		);

		return $components;
	}

	public function getURLAttribute()
	{
		return route('forum.get.view.category', $this->getURLComponents());
	}

	public function getNewThreadURLAttribute()
	{
		return route('forum.post.create.thread', $this->getURLComponents());
	}

	public function getCanPostAttribute()
	{
		return AccessControl::check($this, 'access_category', FALSE);
	}


}
