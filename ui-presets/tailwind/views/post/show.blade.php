@extends ('forum.master', ['breadcrumbs_append' => [trans('forum::posts.view')]])

@section ('content')
    <div id="post">
        <div class="flex flex-row justify-between mb-3">
            <h2 class="grow">{{ trans('forum::posts.view') }} ({{ $thread->title }})</h2>
            <x-forum.button-link href="{{ Forum::route('thread.show', $thread) }}">{{ trans('forum::threads.view') }}</x-forum.button-link>
        </div>

        <hr>

        @include ('forum.post.partials.list', ['post' => $post, 'single' => true])
    </div>
@stop
