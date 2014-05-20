<?php namespace Atrakeur\Forum\Models;

class ForumMessage extends AbstractForumBaseModel {

	protected $table = 'forum_messages';
	public $timestamps = true;
	protected $softDelete = true;

	public function topic() {
		return $this->belongsTo('\Atrakeur\Forum\Models\ForumTopic');
	}

}