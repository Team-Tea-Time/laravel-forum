@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

@include('forum::partials.errorbox')

<legend>{{ trans('forum::base.edit_post') }}</legend>
<p class="lead">
	{{ trans('forum::base.your_editing') }} @include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread', 'post'))
</p>

@include(
	'forum::partials.forms.post',
	array(
		'form_url'					=> $actionAlias,
		'form_classes'			=> '',
		'show_title_field'	=> FALSE,
		'post_content'			=> $post->content,
		'submit_label'			=> 'Update post',
		'cancel_url'				=> $post->thread->url
	)
)
@overwrite
