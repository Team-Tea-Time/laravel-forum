@extends ('forum::layouts.main', ['thread' => null, 'breadcrumbs_append' => [trans('forum::threads.unread_updated')]])

@section ('content')
    <section class="forum-hero">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="forum-hero-kicker">{{ trans_choice('forum::threads.thread', 2) }}</div>
                <h1 class="forum-hero-title">{{ trans('forum::threads.unread_updated') }}</h1>
                <p class="forum-hero-copy">Threads with activity you have not caught up on yet.</p>
            </div>

            @if (!$threads->isEmpty())
                @can ('markThreadsAsRead')
                    <x-forum::button data-open-modal="mark-as-read">
                        <i data-feather="book-open" class="h-4 w-4"></i>
                        {{ trans('forum::general.mark_read') }}
                    </x-forum::button>
                @endcan
            @endif
        </div>
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

    @if (!$threads->isEmpty())
        @can ('markThreadsAsRead')
            @include ('forum::thread.modals.mark-as-read')
        @endcan
    @endif
@stop
