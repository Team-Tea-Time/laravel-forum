@extends ('forum::layouts.master')

@section ('content')
@include ('forum::partials.breadcrumbs', compact('parentCategory', 'category', 'thread'))

<h2>{{ trans('forum::gemeral.new_reply') }} ({{$thread->title}})</h2>

@include (
    'forum::partials.forms.post',
    [
        'form_url'          => $thread->replyRoute,
        'submit_label'      => trans('forum::general.reply'),
        'cancel_url'        => $thread->route
    ]
)
@overwrite
