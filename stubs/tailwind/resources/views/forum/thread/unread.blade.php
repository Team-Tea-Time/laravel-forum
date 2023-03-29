@extends ('forum.master', ['thread' => null, 'breadcrumbs_append' => [trans('forum::threads.unread_updated')]])

@section ('content')
    <div id="new-posts">
        <h2 class="text-3xl text-medium my-4">{{ trans('forum::threads.unread_updated') }}</h2>

        @if (! $threads->isEmpty())
            <div class="">
                @foreach ($threads as $thread)
                    @include ('forum.thread.partials.list')
                @endforeach
            </div>
        @else
            <div class="bg-white shadow rounded text-gray-500 text-center py-4">
                {{ trans('forum::threads.none_found') }}
            </div>
        @endif
    </div>

    @if (! $threads->isEmpty())
        @can ('markThreadsAsRead')
            <div class="flex justify-center mt-4">
                <x-forum.button class="px-5 flex items-center gap-2" data-open-modal="mark-as-read">
                    <i data-feather="book"></i> {{ trans('forum::general.mark_read') }}
                </x-forum.button>
            </div>

            @include ('forum.thread.modals.mark-as-read')
        @endcan
    @endif
@stop
