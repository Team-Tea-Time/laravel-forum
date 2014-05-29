<?php namespace Atrakeur\Forum\Models;

class ForumCategory extends AbstractForumBaseModel {

	protected $table      = 'forum_categories';
	public    $timestamps = false;
	protected $softDelete = false;
	protected $appends    = array('topicCount', 'replyCount', 'lastReply', 'url');

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

	public function getLastReplyAttribute()
	{
		$lastReplyId = $this->rememberAttribute('lastReply', function() {

			// list topics in this category
			$topics = $this->topics()->lists('id');
			if (count($topics) > 0)
			{
				//get last message
				$message = ForumMessage::whereTopicIn($topics)->orderBy('updated_at', 'DESC')->limit(1)->first();
				if ($message != NULL) 
				{	
					//store id in cache
					return $message->id;
				}
			}

			return NULL;
		});

		//Get the last message
		//validate existence or clear orphaned cache data
		$message = ForumMessage::find($lastReplyId);
		if ($message != NULL) 
		{
			return $message;
		}
		else
		{
			$this->clearAttributeCache();
		}
		
		return NULL;
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
