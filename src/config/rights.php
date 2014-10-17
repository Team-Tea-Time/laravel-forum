<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Can post in category
	|--------------------------------------------------------------------------
	|
	| Callback used to determine if a given user is allowed to post in a given
	| category. Return true if user is allowed to post a new topic. Any other
	| return type will be used like false.
	|
	*/
	'postcategory' => function(\Atrakeur\Forum\Models\ForumCategory $category, $user) {
		//Here we allow only logged in users
		if ($user != NULL)
		{
			return true;
		}

		return false;
	},

	/*
	|--------------------------------------------------------------------------
	| Can post in topic
	|--------------------------------------------------------------------------
	|
	| Callback used to determine if a given user is allowed to post in a given
	| topic. Return true if the user can respond to this topic. Any other return
	| value will be used like false.
	|
	*/
	'posttopic' => function(\Atrakeur\Forum\Models\ForumTopic $topic, $user) {
		//Here we allow only logged in users
		if ($user != NULL)
		{
			return true;
		}

		return false;
	},

	/*
	|--------------------------------------------------------------------------
	| Can post in message
	|--------------------------------------------------------------------------
	|
	| Callback used to determine if a given user is allowed to edit a given
	| message. Return true if the user can edit this message, false otherwise.
	|
	*/
	'postmessage' => function(\Atrakeur\Forum\Models\ForumMessage $message, $user) {
		//Here we allow only logged in users
		if ($user != NULL)
		{
			//Here we allow only user to edit their own messages
			if ($user->id == $message->author)
			{
				return true;
			}
		}

		return false;
	},



);
