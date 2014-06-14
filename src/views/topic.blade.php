@include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))

@include('forum::partials.postbutton')->with(array('message' => 'New Reply', 'url' => $topic->postUrl))
<table class="table table-index">
	<thead>
		<tr>
			<td>
				Auteur
			</td>
			<td>
				Message
			</td>
		</tr>
	</thead>
	<tbody>
		@foreach ($messages as $message)
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
				@include('forum::partials.postbutton')->with(array('message' => 'Edit', 'url' => $message->postUrl))
			</td>
			<td>
				Posted at {{ $message->created_at }}
				@if ($message->updated_at != null && $message->created_at != $message->updated_at)
					Last update at {{ $message->updated_at }}
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>	
</table>
{{ $messages->links() }}
