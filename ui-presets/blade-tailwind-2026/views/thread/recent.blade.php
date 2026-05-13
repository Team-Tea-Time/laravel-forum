@extends ('forum::layouts.main', ['thread' => null, 'breadcrumbs_append' => [trans('forum::threads.recent')]])

@section ('content')
    <section class="forum-hero">
        <div class="forum-hero-kicker">{{ trans_choice('forum::threads.thread', 2) }}</div>
        <h1 class="forum-hero-title">{{ trans('forum::threads.recent') }}</h1>
        <p class="forum-hero-copy">A live view of the newest activity across every public discussion.</p>
    </section>

    <div id="new-posts">
        @if (!$threads->isEmpty())
            <div class="space-y-3">
                @foreach ($threads as $thread)
                    @include ('forum::thread.partials.list')
                @endforeach
            </div>
        @else
            <div class="forum-empty">
                {{ trans('forum::threads.none_found') }}
            </div>
        @endif
    </div>
@stop
