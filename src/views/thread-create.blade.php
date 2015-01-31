@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

{{ Form::open(array('url' => $category->newThreadRoute, 'class' => 'form-horizontal')) }}
<fieldset>

<!-- Form Name -->
<legend>{{ trans('forum::base.new_thread') }}</legend>
<p class="lead">
	{{ trans('forum::base.posting_into') }} @include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))
</p>

<div class="control-group">
	<label class="control-label" for="title">{{ trans('forum::base.title') }}</label>
	<div class="controls">
		{{ Form::text('title') }}
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="content">{{ trans('forum::base.your_post') }}</label>
	<div class="controls">
		{{ Form::textarea('content') }}
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
