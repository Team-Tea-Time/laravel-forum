@include('forum::partials.csslinks')

@include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))

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
				
			</td>
			<td>
				{{ $message->created_at }}
			</td>
		</tr>
		@endforeach
	</tbody>	
</table>
{{ $messages->links() }}
