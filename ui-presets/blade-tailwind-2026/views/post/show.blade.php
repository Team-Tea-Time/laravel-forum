@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::posts.view')]])

@section ('content')
    <section class="forum-hero">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="forum-hero-kicker">{{ trans('forum::posts.view') }}</div>
                <h1 class="forum-hero-title">{{ $thread->title }}</h1>
            </div>
            <x-forum::button-link href="{{ Forum::route('thread.show', $thread) }}">{{ trans('forum::threads.view') }}</x-forum::button-link>
        </div>
    </section>

    @include ('forum::post.partials.list', ['post' => $post, 'single' => true])
@stop
