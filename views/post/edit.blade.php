@extends ('forum::master', ['breadcrumb_other' => trans('forum::posts.edit')])

@section ('content')
    <h2>{{ trans('forum::posts.edit') }} ({{ $thread->title }})</h2>

    @if ($post->parent)
        <h3>{{ trans('forum::general.response_to') }}...</h3>

        @include ('forum::post.partials.excerpt', ['post' => $post->parent])
    @endif

    @include (
        'forum::post.partials.edit',
        [
            'form_url'          => $post->editRoute,
            'method'            => 'PATCH',
            'post_content'      => $post->content,
            'submit_label'      => trans('forum::posts.edit'),
            'cancel_url'        => $post->thread->route
        ]
    )
@overwrite
