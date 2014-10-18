<?php namespace Atrakeur\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ForumMessage extends AbstractForumBaseModel {

	use SoftDeletingTrait;

	protected $table      = 'forum_messages';
	public    $timestamps = true;
	protected $dates      = ['deleted_at'];
	protected $appends    = array('url', 'postUrl', 'canPost');
	protected $guarded    = array('id');

	public function topic()
	{
		return $this->belongsTo('\Atrakeur\Forum\Models\ForumTopic', 'parent_topic');
	}

	public function author()
	{
		return $this->belongsTo(\Config::get('forum::integration.usermodel'), 'author_id');
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

	public function getPostUrlAttribute()
	{
		$topic    = $this->topic;
		$category = $topic->category;

		return action(\Config::get('forum::integration.postcontroller').'@postEditMessage',
			array(
				'categoryId'  => $category->id,
				'categoryUrl' => \Str::slug($category->title, '_'),
				'topicId'     => $topic->id,
				'topicUrl'    => \Str::slug($topic->title, '_'),
				'messageId'   => $this->id
			)
		);
	}

	public function getCanPostAttribute()
	{
		return $this->computeCanPostAttribute('rights.postmessage');
	}

}
