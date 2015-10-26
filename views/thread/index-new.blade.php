@extends ('forum::master', ['breadcrumb_other' => trans('forum::threads.new_updated')])

@section ('content')
    <h2>{{ trans('forum::threads.new_updated') }}</h2>

    @if (!$threads->isEmpty())
        <table class="table table-index">
            <thead>
                <tr>
                    <th>{{ trans('forum::general.subject') }}</th>
                    <th class="col-md-2">{{ trans('forum::general.replies') }}</th>
                    <th class="col-md-2 text-right">{{ trans('forum::posts.last') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($threads as $thread)
                    <tr>
                        <td>
                            <span class="pull-right">
                                @if ($thread->locked)
                                    <span class="label label-danger">{{ trans('forum::threads.locked') }}</span>
                                @endif
                                @if ($thread->pinned)
                                    <span class="label label-info">{{ trans('forum::threads.pinned') }}</span>
                                @endif
                                @if ($thread->userReadStatus)
                                    <span class="label label-primary">{{ trans($thread->userReadStatus) }}</span>
                                @endif
                            </span>
                            <p class="lead">
                                <a href="{{ $thread->route }}">{{ $thread->title }}</a>
                            </p>
                            <p>
                                {{ $thread->authorName }}
                                <span class="text-muted">(<em><a href="{{ $thread->category->route }}">{{ $thread->category->title }}</a></em>, {{ $thread->posted }})</span>
                            </p>
                        </td>
                        <td>
                            {{ $thread->replyCount }}
                        </td>
                        <td class="text-right">
                            {{ $thread->lastPost->authorName }}
                            <p class="text-muted">({{ $thread->lastPost->posted }})</p>
                            <a href="{{ $thread->lastPostUrl }}" class="btn btn-primary btn-xs">{{ trans('forum::posts.view') }} &raquo;</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @can ('markNewThreadsAsRead')
            <div class="text-center">
                <form action="{{ route('forum.mark-new') }}" method="POST" data-confirm>
                    {!! method_field('patch') !!}
                    <button class="btn btn-primary btn-small">{{ trans('forum::general.mark_read') }}</button>
                </form>
            </div>
        @endcan
    @else
        <p class="text-center">
            {{ trans('forum::threads.none_found') }}
        </p>
    @endif
@stop
