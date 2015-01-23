@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

<h2>{{ trans('forum::base.new_reply') }}</h2>
<p class="lead">
	{{ trans('forum::posting_into') }} @include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))
</p>

@include(
	'forum::partials.forms.post',
	array(
		'form_url'					=> $thread->replyURL,
		'form_classes'			=> '',
		'show_title_field'	=> FALSE,
		'post_content'			=> '',
		'submit_label'			=> 'Post reply',
		'cancel_url'				=> $thread->URL
	)
)
@overwrite
