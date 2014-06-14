<?php namespace Atrakeur\Forum\Models;

class ForumTopic extends AbstractForumBaseModel
{
	protected $table      = 'forum_topics';
	public    $timestamps = true;
	protected $softDelete = true;
	protected $appends    = array('url');

	public function category()
	{
		return $this->belongsTo('\Atrakeur\Forum\Models\ForumCategory', 'parent_category');
	}

	public function messages()
	{
		return $this->hasMany('\Atrakeur\Forum\Models\ForumMessage', 'parent_topic');
	}

	public function getUrlAttribute()
	{
		return action(\Config::get('forum::integration.viewcontroller').'@getTopic',
			array(
				'categoryId'  => $this->category->id,
				'categoryUrl' => \Str::slug($this->category->title, '_'),
				'topicId'     => $this->id,
				'topicUrl'    => \Str::slug($this->title, '_'),
			)
		);
	}

	public function getPostUrlAttribute()
	{
		return action(\Config::get('forum::integration.postcontroller').'@postNewMessage',
			array(
				'categoryId'  => $this->category->id,
				'categoryUrl' => \Str::slug($this->category->title, '_'),
				'topicId'     => $this->id,
				'topicUrl'    => \Str::slug($this->title, '_'),
			)
		);
	}

}
