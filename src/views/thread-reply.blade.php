@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

@include('forum::partials.errorbox')

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

<h2>{{ trans('forum::base.post_post') }}</h2>
<p class="lead">
	{{ trans('forum::posting_into') }} @include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))
</p>

@include(
	'forum::partials.forms.post',
	array(
		'form_url'					=> $actionAlias,
		'form_classes'			=> '',
		'show_title_field'	=> FALSE,
		'post_content'			=> '',
		'submit_label'			=> 'Post reply',
		'cancel_url'				=> $thread->URL
	)
)
@overwrite
