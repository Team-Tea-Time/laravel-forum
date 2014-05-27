<?php namespace Atrakeur\Forum\Models;

class ForumMessage extends AbstractForumBaseModel {

	protected $table      = 'forum_messages';
	public    $timestamps = true;
	protected $softDelete = true;
	protected $appends    = array('url');

	public function topic()
	{
		return $this->belongsTo('\Atrakeur\Forum\Models\ForumTopic', 'parent_topic');
	}

	public function author()
	{
		return $this->belongsTo(\Config::get('forum::usermodel'));
	}

	public function scopeWhereTopicIn($query, Array $topics) 
	{
		if (count($topics) == 0) 
		{
			return $query;
		}

		return $query->whereIn('parent_topic', $topics);
	}

	public function getUrlAttribute()
	{
		//TODO add page get parameter
		return $this->topic->url;
	}

}
