<?php namespace Atrakeur\Forum\Models;

class ForumTopic extends AbstractForumBaseModel
{
	protected $table = 'forum_topics';
	public $timestamps = true;
	protected $softDelete = true;

	public function category()
    {
		return $this->hasOne('\Atrakeur\Forum\Models\ForumCategory');
	}

	public function messages()
    {
		return $this->hasMany('\Atrakeur\Forum\Models\ForumMessage');
	}

}
