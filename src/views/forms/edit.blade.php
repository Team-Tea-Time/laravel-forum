@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

{{ Form::open(array('url' => $post->editRoute, 'class' => 'form-horizontal')) }}
<fieldset>

<legend>{{ trans('forum::base.edit_post') }}</legend>
<p class="lead">
	{{ trans('forum::base.you_are_editing') }} @include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread', 'post'))
</p>

<div class="control-group">
	<label class="control-label" for="textarea">{{ trans('forum::base.label_your_post') }}</label>
	<div class="controls">
		{{ Form::textarea('content', $post->content) }}
	</div>
</div>

<div class="control-group">
	<div class="controls">
		{{ Form::submit(trans('forum::base.send')) }}
	</div>
</div>

</fieldset>
{{ Form::close() }}
@overwrite
