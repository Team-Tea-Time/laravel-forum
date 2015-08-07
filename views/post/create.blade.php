@extends ('forum::master', ['breadcrumb_other' => trans('forum::general.new_reply')])

@section ('content')
    <h2>{{ trans('forum::general.new_reply') }} ({{ $thread->title }})</h2>

    @if (!is_null($post))
        <h3>{{ trans('forum::general.replying_to', ['item' => $post->authorName]) }}...</h3>

        @include ('forum::post.partials.excerpt')
    @endif

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
