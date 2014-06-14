@include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))

<p class="lead">
	You're posting into @include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))
</p>

@if (isset($prevMessages) && count($prevMessages) > 0)
<p class="lead">
	Lastests messages
</p>
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
		@foreach ($prevMessages as $message)
			@include('forum::partials.message')->with(compact('message'))
		@endforeach
	</tbody>	
</table>
@endif

{{ Form::open(array('url' => $actionUrl)) }}

Message: {{ Form::textarea('data') }}

{{ Form::submit('Send') }}
{{ Form::close() }}
