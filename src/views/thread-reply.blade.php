@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

<h2>{{ trans('forum::base.new_reply') }} ({{$thread->title}})</h2>

@include(
    'forum::partials.forms.post',
    array(
        'form_url'          => $thread->replyRoute,
        'form_classes'      => '',
        'show_title_field'  => false,
        'post_content'      => '',
        'submit_label'      => trans('forum::base.reply'),
        'cancel_url'        => $thread->route
    )
)
@overwrite
