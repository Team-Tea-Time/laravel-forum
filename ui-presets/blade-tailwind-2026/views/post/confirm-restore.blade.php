@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans_choice('forum::posts.restore', 1)]])

@section ('content')
    <section class="forum-hero">
        <div class="forum-hero-kicker">{{ trans_choice('forum::posts.restore', 1) }}</div>
        <h1 class="forum-hero-title">{{ trans_choice('forum::posts.restore', 1) }}</h1>
    </section>

    @include ('forum::post.partials.list', ['post' => $post, 'single' => true])

    <form method="POST" action="{{ Forum::route('post.restore', $post) }}" class="forum-panel mt-5 space-y-5 p-5 sm:p-6">
        @csrf
        @method('POST')

        <p class="forum-muted">{{ trans('forum::general.generic_confirm') }}</p>

        <div class="flex justify-end gap-3">
            <x-forum::button-link href="{{ URL::previous() }}" class="forum-button-secondary">{{ trans('forum::general.cancel') }}</x-forum::button-link>
            <x-forum::button type="submit">{{ trans('forum::general.restore') }}</x-forum::button>
        </div>
    </form>
@stop
