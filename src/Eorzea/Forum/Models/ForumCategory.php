<?php namespace Eorzea\Forum\Models;

use \Eorzea\Forum\Models\ForumThread;

class ForumCategory extends AbstractForumBaseModel {

	protected $table      = 'forum_categories';
	public    $timestamps = false;
	protected $appends    = array('threadCount', 'replyCount', 'url', 'postAlias', 'canPost');

	public function parentCategory()
	{
		return $this->belongsTo('\Eorzea\Forum\Models\ForumCategory', 'parent_category');
	}

	public function subcategories()
	{
		return $this->hasMany('\Eorzea\Forum\Models\ForumCategory', 'parent_category');
	}

	public function threads()
	{
		return $this->hasMany('\Eorzea\Forum\Models\ForumThread', 'parent_category');
	}

	public function scopeWhereTopLevel($query)
	{
		return $query->where('parent_category', '=', NULL);
	}

	public function getThreadCountAttribute()
	{
		return $this->rememberAttribute('threadCount', function(){
			return $this->threads()->count();
		});
	}

	public function getReplyCountAttribute()
	{
		return $this->rememberAttribute('replyCount', function(){
			$replyCount = 0;

			$threadsIDs = array();
			$threads    = $this->threads()->get(array('id'));

			foreach ($threads AS $thread) {
				$threadsIDs[] = $thread->id;
			}

			if (!empty($threadsIDs))
			{
				$replyCount = ForumPost::whereIn('parent_thread', $threadsIDs)->count();
			}
			return $replyCount;
		});
	}

	public function getAliasAttribute()
	{
		return action(Config::get('forum::integration.controller').'@getCategory',
			array(
				'categoryID' => $this->id,
				'categoryAlias' => Str::slug($this->title, '_')
			)
		);
	}

	public function getPostAliasAttribute()
	{
		return action(\Config::get('forum::integration.postcontroller').'@postNewThread',
			array(
				'categoryID' => $this->id,
				'categoryAlias' => Str::slug($this->title, '_')
			)
		);
	}

	public function getCanPostAttribute()
	{
		return $this->computeCanPostAttribute('rights.postcategory');
	}


}
