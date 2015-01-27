<?php namespace Riari\Forum\Models;

use Riari\Forum\Models\Thread;
use Riari\Forum\Libraries\AccessControl;

use Config;
use Str;

class Category extends BaseModel {

	protected $table      = 'forum_categories';
	public    $timestamps = false;
	protected $appends    = ['threadCount', 'replyCount', 'Route', 'newThreadRoute'];

	public function parentCategory()
	{
		return $this->belongsTo('\Riari\Forum\Models\Category', 'parent_category')->orderBy('weight');
	}

	public function subcategories()
	{
		return $this->hasMany('\Riari\Forum\Models\Category', 'parent_category')->orderBy('weight');
	}

	public function threads()
	{
		return $this->hasMany('\Riari\Forum\Models\Thread', 'parent_category')->with('category', 'posts')->orderBy('pinned', 'desc')->orderBy('updated_at', 'desc');
	}

	public function getThreadsPaginatedAttribute()
	{
		return $this->threads()->paginate(Config::get('forum::preferences.threads_per_category'));
	}

	public function getPageLinksAttribute()
	{
		return $this->threadsPaginated->links(Config::get('forum::preferences.pagination_view'));
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

	protected function getRouteComponents()
	{
		$components = array(
			'categoryID'		=> $this->id,
			'categoryAlias'	=> Str::slug($this->title, '-')
		);

		return $components;
	}

	public function getRouteAttribute()
	{
		return $this->getRoute('forum.get.view.category');
	}

	public function getNewThreadRouteAttribute()
	{
		return $this->getRoute('forum.post.create.thread');
	}

	public function getCanPostAttribute()
	{
		return AccessControl::check($this, 'access_category', FALSE);
	}


}
