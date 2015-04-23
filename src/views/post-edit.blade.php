@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

<h2>{{ trans('forum::base.edit_post') }} ({{$thread->title}})</h2>

@include(
    'forum::partials.forms.post',
    array(
        'form_url'          => $post->editRoute,
        'form_classes'      => '',
        'show_title_field'  => false,
        'post_content'      => $post->content,
        'submit_label'      => trans('forum::base.edit_post'),
        'cancel_url'        => $post->thread->route
    )
)
@overwrite
