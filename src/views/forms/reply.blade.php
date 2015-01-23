@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

@if (isset($prevposts) && count($prevposts) > 0)
<p class="lead">
	{{ trans('forum::base.latest_posts') }}
</p>
<table class="table table-index">
	<thead>
		<tr>
			<td>
				{{ trans('forum::base.author') }}
			</td>
			<td>
				{{ trans('forum::base.post') }}
			</td>
		</tr>
	</thead>
	<tbody>
		@foreach ($prevposts as $post)
			@include('forum::partials.post', compact('post'))
		@endforeach
	</tbody>
</table>
@endif

{{ Form::open(array('url' => $thread->replyRoute, 'class' => 'form-horizontal')) }}
<fieldset>

<legend>{{ trans('forum::base.post_post') }}</legend>
<p class="lead">
	{{ trans('forum::posting_into') }} @include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))
</p>

<div class="control-group">
	<label class="control-label" for="textarea">{{ trans('forum::base.label_your_post') }}</label>
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
