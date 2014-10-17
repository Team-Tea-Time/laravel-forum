@include('forum::partials.pathdisplay', compact('parentCategory', 'category', 'topic'))

@include('forum::partials.errorbox')

{{ Form::open(array('url' => $actionUrl, 'class' => 'form-horizontal')) }}
<fieldset>

<legend>{{ trans('forum::base.edit_message') }}</legend>
<p class="lead">
	{{ trans('forum::base.your_editing') }} @include('forum::partials.pathdisplay', compact('parentCategory', 'category', 'topic', 'message'))
</p>

<div class="control-group">
	<label class="control-label" for="textarea">{{ trans('forum::base.label_your_message') }}</label>
	<div class="controls">
		{{ Form::textarea('data', $message->data) }}
	</div>
</div>

<div class="control-group">
	<div class="controls">
		{{ Form::submit(trans('forum::base.send')) }}
	</div>
</div>

</fieldset>
{{ Form::close() }}
