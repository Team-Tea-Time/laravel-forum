<tr>
	<td>
		Un auteur
	</td>
	<td>
		{{ $message->data }}
	</td>
</tr>
<tr>
	<td>
		@include('forum::partials.postbutton', array('message' => 'Edit', 'url' => $message->postUrl))
	</td>
	<td>
		Posted at {{ $message->created_at }}
		@if ($message->updated_at != null && $message->created_at != $message->updated_at)
			Last update at {{ $message->updated_at }}
		@endif
	</td>
</tr>