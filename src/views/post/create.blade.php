@extends ('forum::master')

@section ('content')
@include ('forum::partials.breadcrumbs', compact('category', 'thread'))

<h2>{{ trans('forum::general.new_reply') }} ({{$thread->title}})</h2>

@include (
    'forum::post.partials.edit',
    [
        'form_url'          => $thread->replyRoute,
        'method'            => 'POST',
        'show_title_field'  => false,
        'submit_label'      => trans('forum::general.reply'),
        'cancel_url'        => $thread->route
    ]
)
@overwrite
