@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

<h2>{{{ $thread->title }}}</h2>

@if( $thread->canPost )
<p>
	<a href="{{ $thread->postAlias }}" class="button radius small">New reply</a>
	<a href="#quick-reply" class="button radius small right">Quick reply</a>
</p>
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

@if( $thread->canPost )
<h3>Quick reply</h3>
<div id="quick-reply">
	@include(
		'forum::partials.forms.post',
		array(
			'form_url'					=> $thread->postAlias,
			'form_classes'			=> '',
			'show_title_field'	=> FALSE,
			'post_content'			=> '',
			'submit_label'			=> 'Post reply',
			'cancel_url'				=> ''
		)
	)
</div>
@endif

@overwrite
