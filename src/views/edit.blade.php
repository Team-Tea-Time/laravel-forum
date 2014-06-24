@include('forum::partials.pathdisplay', compact('parentCategory', 'category', 'topic'))

@include('forum::partials.errorbox')

{{ Form::open(array('url' => $actionUrl, 'class' => 'form-horizontal')) }}
<fieldset>

<legend>Edit a message</legend>
<p class="lead">
	You're editing @include('forum::partials.pathdisplay', compact('parentCategory', 'category', 'topic', 'message'))
</p>

<div class="control-group">
	<label class="control-label" for="textarea">Your message</label>
	<div class="controls">                     
		{{ Form::textarea('data', $message->data) }}
	</div>
</div>

<div class="control-group">
	<div class="controls">                     
		{{ Form::submit('Send') }}
	</div>
</div>

</fieldset>
{{ Form::close() }}
