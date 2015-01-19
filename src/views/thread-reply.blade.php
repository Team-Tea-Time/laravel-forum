@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

@include('forum::partials.errorbox')

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
