@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

<h2>
	@if($thread->locked)
	[{{ trans('forum::base.locked') }}]
	@endif
	@if($thread->pinned)
	[{{ trans('forum::base.pinned') }}]
	@endif
	{{{ $thread->title }}}
</h2>

@if($thread->canDelete)
<a href="{{ $thread->deleteURL }}" class="btn btn-default">{{ trans('forum::base.delete_thread') }}</a>
@endif

@if($thread->canPost)
<div class="btn-group" role="group">
	<a href="{{ $thread->replyURL }}" class="btn btn-default">{{ trans('forum::base.new_reply') }}</a>
	<a href="#quick-reply" class="btn btn-default">{{ trans('forum::base.quick_reply') }}</a>
</div>
@endif

<table>
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
		@foreach($thread->posts as $post)
		@include('forum::partials.post', compact('post'))
		@endforeach
	</tbody>
</table>

{{ $paginationLinks }}

@if($thread->canPost)
<h3>{{ trans('forum::base.quick_reply') }}</h3>
<div id="quick-reply">
	@include(
		'forum::partials.forms.post',
		array(
			'form_url'					=> $thread->replyURL,
			'form_classes'			=> '',
			'show_title_field'	=> FALSE,
			'post_content'			=> '',
			'submit_label'			=> trans('forum::base.post_reply'),
			'cancel_url'				=> ''
		)
	)
</div>
@endif

@overwrite
