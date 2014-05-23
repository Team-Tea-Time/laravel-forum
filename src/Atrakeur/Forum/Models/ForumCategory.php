<?php namespace Atrakeur\Forum\Models;

class ForumCategory extends AbstractForumBaseModel {

	protected $table      = 'forum_categories';
	public    $timestamps = false;
	protected $softDelete = false;
	protected $appends    = array('topicCount', 'replyCount', 'lastReplyId', 'url');

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
		$topicCount = $this->rememberAttribute('topicCount', function() {

			return $this->topics()->count();
			
		});
		return $topicCount;
	}

	public function getReplyCountAttribute()
	{
		$replyCount = $this->rememberAttribute('replyCount', function() {

			$replyCount = 0;
			$topics     = $this->topics()->with('messages')->get();
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

	public function getUrlAttribute()
	{
		return action(\Config::get('forum::integration.forumcontroller').'@getCategory',
			array(
				'categoryId' => $this->id,
				'categoryUrl' => \Str::slug($this->title, '_')
			)
		);
	}

}
