@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

<legend>{{ trans('forum::base.edit_post') }}</legend>
<p class="lead">
	{{ trans('forum::base.you_are_editing') }} @include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread', 'post'))
</p>

@include(
	'forum::partials.forms.post',
	array(
		'form_url'					=> $post->editRoute,
		'form_classes'			=> '',
		'show_title_field'	=> FALSE,
		'post_content'			=> $post->content,
		'submit_label'			=> 'Update post',
		'cancel_url'				=> $post->thread->Route
	)
)
@overwrite
