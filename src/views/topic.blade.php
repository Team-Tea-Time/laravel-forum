@include('forum::partials.pathdisplay')

@include('forum::partials.postbutton', array('message' => 'New Reply', 'url' => $topic->postUrl, 'accessModel' => $topic))
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
			@include('forum::partials.message', compact('message'))
		@endforeach
	</tbody>	
</table>
{{ $paginationLinks }}
