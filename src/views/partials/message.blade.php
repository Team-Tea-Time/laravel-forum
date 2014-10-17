<tr>
	<td>
		{{{ $message->author->username }}}
	</td>
	<td>
		{{ nl2br(e($message->data)) }}
	</td>
</tr>
<tr>
	<td>
		@include('forum::partials.postbutton', array('message' => 'Edit', 'url' => $message->postUrl, 'accessModel' => $message))
	</td>
	<td>
		{{ trans('forum::base.posted_at') }} {{ $message->created_at }}
		@if ($message->updated_at != null && $message->created_at != $message->updated_at)
			{{ trans('forum::base.last_update') }} {{ $message->updated_at }}
		@endif
	</td>
</tr>