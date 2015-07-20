@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

<h2>{{ trans('forum::threads.new_thread') }} ({{$category->title}})</h2>

@include(
    'forum::post.partials.edit',
    array(
        'form_url'            => $category->newThreadRoute,
        'form_classes'        => '',
        'show_title_field'    => true,
        'post_content'        => '',
        'submit_label'        => trans('forum::general.send'),
        'cancel_url'          => ''
    )
)
@overwrite
