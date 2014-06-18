@include('forum::partials.pathdisplay')

@include('forum::partials.postbutton')
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
