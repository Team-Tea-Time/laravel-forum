@include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))

@include('forum::partials.errorbox')

{{ Form::open(array('url' => $actionUrl, 'class' => 'form-horizontal')) }}
<fieldset>

<!-- Form Name -->
<legend>Post a new topic</legend>
<p class="lead">
	You're posting into @include('forum::partials.pathdisplay')->with(compact('parentCategory', 'category', 'topic'))
</p>

<div class="control-group">
	<label class="control-label" for="title">Title</label>
	<div class="controls">                     
		{{ Form::text('title') }}
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="data">Your message</label>
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
