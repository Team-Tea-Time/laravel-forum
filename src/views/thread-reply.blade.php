@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

<h2>{{ trans('forum::base.new_reply') }}</h2>
<p class="lead">
	{{ trans('forum::base.posting_into') }} <em>{{ $thread->title }}</em>
</p>

@include(
	'forum::partials.forms.post',
	array(
		'form_url'					=> $thread->replyRoute,
		'form_classes'			=> '',
		'show_title_field'	=> FALSE,
		'post_content'			=> '',
		'submit_label'			=> 'Post reply',
		'cancel_url'				=> $thread->Route
	)
)
@overwrite
