<?php namespace Atrakeur\Forum\Models;

class ForumCategory extends AbstractForumBaseModel {

	protected $table = 'forum_categories';
	public $timestamps = false;
	protected $softDelete = false;
	protected $appends = array('topicCount', 'replyCount', 'lastReplyId');

	public function parentCategory()
	{
		return $this->hasOne('\Atrakeur\Forum\Models\ForumCategory', 'parent_category');
	}

	public function subcategories()
	{
		return $this->hasMany('\Atrakeur\Forum\Models\ForumCategory', 'parent_category');
	}

	public function topics()
	{
		return $this->belongsTo('\Atrakeur\Forum\Models\ForumTopic');
	}

	public function scopeWhereTopLevel($query)
	{
		return $query->where('parent_category', '=', NULL);
	}

	public function getTopicCountAttribute()
	{
		$topicCount = $this->rememberAttribute('topicCount', function() {
			echo 'load';
			return $this->topics()->count();
		});
		return $topicCount;
	}

	public function getReplyCountAttribute()
	{
		$replyCount = $this->rememberAttribute('replyCount', function() {
			$replyCount = 0;
			$topics = $this->topics()->with('messages')->get();
			foreach ($topics as $topic) {
				$replyCount += $topic->messages->count();
			}
			return $replyCount;
		});
		return $replyCount;   
	}

	public function getLastReplyIdAttribute()
	{
		//TODO
	}

}