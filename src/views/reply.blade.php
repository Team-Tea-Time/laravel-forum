@include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))

@include('forum::partials.errorbox')

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

{{ Form::open(array('url' => $actionUrl, 'class' => 'form-horizontal')) }}
<fieldset>

<!-- Form Name -->
<legend>Post a new message</legend>
<p class="lead">
	You're posting into @include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))
</p>

<div class="control-group">
	<label class="control-label" for="textarea">Your message</label>
	<div class="controls">                     
		{{ Form::textarea('data') }}
	</div>
</div>

<div class="control-group">
	<div class="controls">                     
		{{ Form::submit('Send') }}
	</div>
</div>

</fieldset>
{{ Form::close() }}
