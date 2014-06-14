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
			@include('forum::partials.message')->with(compact('message'))
		@endforeach
	</tbody>	
</table>
{{ $messages->links() }}
