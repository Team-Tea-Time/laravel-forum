<?php namespace Atrakeur\Forum\Models;

use \Atrakeur\Forum\Models\ForumTopic;

class ForumCategory extends AbstractForumBaseModel {

	protected $table      = 'forum_categories';
	public    $timestamps = false;
	protected $appends    = array('topicCount', 'replyCount', 'url', 'postUrl', 'canPost');

	public function parentCategory()
	{
		return $this->belongsTo('\Atrakeur\Forum\Models\ForumCategory', 'parent_category');
	}

	public function subcategories()
	{
		return $this->hasMany('\Atrakeur\Forum\Models\ForumCategory', 'parent_category');
	}

	public function topics()
	{
		return $this->hasMany('\Atrakeur\Forum\Models\ForumTopic', 'parent_category');
	}

	public function scopeWhereTopLevel($query)
	{
		return $query->where('parent_category', '=', NULL);
	}

	public function getTopicCountAttribute()
	{
		return $this->rememberAttribute('topicCount', function(){
			return $this->topics()->count();
		});
	}

	public function getReplyCountAttribute()
	{
		return $this->rememberAttribute('replyCount', function(){
			$replyCount = 0;

			$topicsIds = array();
			$topics    = $this->topics()->get(array('id'));

			foreach ($topics AS $topic) {
				$topicsIds[] = $topic->id;
			}

			if (!empty($topicsIds)) 
			{
				$replyCount = ForumMessage::whereIn('parent_topic', $topicsIds)->count();
			}
			return $replyCount;
		});
	}

	public function getUrlAttribute()
	{
		return action(\Config::get('forum::integration.viewcontroller').'@getCategory',
			array(
				'categoryId' => $this->id,
				'categoryUrl' => \Str::slug($this->title, '_')
			)
		);
	}

	public function getPostUrlAttribute()
	{
		return action(\Config::get('forum::integration.postcontroller').'@postNewTopic',
			array(
				'categoryId' => $this->id,
				'categoryUrl' => \Str::slug($this->title, '_')
			)
		);
	}

	public function getCanPostAttribute()
	{
		return $this->computeCanPostAttribute('rights.postcategory');
	}


}
