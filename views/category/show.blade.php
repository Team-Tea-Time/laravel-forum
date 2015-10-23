{{-- $thread is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends ('forum::master', ['thread' => null])

@section ('content')
    <div id="category">
        @can ('createCategories')
            @include ('forum::category.partials.form-create')
        @endcan

        <h2>
            @if ($category->trashed())
                <span class="label label-danger">{{ trans('forum::general.deleted') }}</span>
            @endif
            {{ $category->title }}
        </h2>

        <hr>

        @can ('manageCategories')
            <form action="{{ route('forum.category.update', $category->id) }}" method="POST" data-actions-form>
                {!! csrf_field() !!}
                {!! method_field('patch') !!}

                @include ('forum::category.partials.actions')
            </form>
        @endcan

        @if (!$category->children->isEmpty())
            <table class="table table-category">
                <thead>
                    <tr>
                        <th>{{ trans_choice('forum::categories.category', 1) }}</th>
                        <th class="col-md-2">{{ trans_choice('forum::threads.thread', 2) }}</th>
                        <th class="col-md-2">{{ trans_choice('forum::posts.post', 2) }}</th>
                        <th class="col-md-2">{{ trans('forum::threads.newest') }}</th>
                        <th class="col-md-2">{{ trans('forum::posts.last') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($category->children as $subcategory)
                        @include ('forum::category.partials.list', ['category' => $subcategory])
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="row">
            <div class="col-xs-4">
                @can ('createThreads', $category)
                    <a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                @endcan
            </div>
            <div class="col-xs-8 text-right">
                {!! $category->threadsPaginated->render() !!}
            </div>
        </div>

        @can ('manageThreads', $category)
            <form action="{{ route('forum.bulk.thread.update') }}" method="POST" data-actions-form>
                {!! csrf_field() !!}
                {!! method_field('delete') !!}
        @endcan

        @if ($category->threadsAllowed)
            <table class="table table-thread">
                <thead>
                    <tr>
                        <th>{{ trans('forum::general.subject') }}</th>
                        <th class="col-md-2 text-right">{{ trans('forum::general.replies') }}</th>
                        <th class="col-md-2 text-right">{{ trans('forum::posts.last') }}</th>
                        @can ('manageThreads', $category)
                            <th class="col-md-1 text-right"><input type="checkbox" data-toggle-all></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @if (!$category->threadsPaginated->isEmpty())
                        @foreach ($category->threadsPaginated as $thread)
                            <tr class="{{ $thread->trashed() ? "deleted" : "" }}">
                                <td>
                                    <span class="pull-right">
                                        @if ($thread->locked)
                                            <span class="label label-warning">{{ trans('forum::threads.locked') }}</span>
                                        @endif
                                        @if ($thread->pinned)
                                            <span class="label label-info">{{ trans('forum::threads.pinned') }}</span>
                                        @endif
                                        @if ($thread->userReadStatus && !$thread->trashed())
                                            <span class="label label-primary">{{ trans($thread->userReadStatus) }}</span>
                                        @endif
                                        @if ($thread->trashed())
                                            <span class="label label-danger">{{ trans('forum::general.deleted') }}</span>
                                        @endif
                                    </span>
                                    <p class="lead">
                                        <a href="{{ $thread->route }}">{{ $thread->title }}</a>
                                    </p>
                                    <p>{{ $thread->authorName }} <span class="text-muted">({{ $thread->posted }})</span></p>
                                </td>
                                @if ($thread->trashed())
                                    <td colspan="2">&nbsp;</td>
                                @else
                                    <td class="text-right">
                                        {{ $thread->replyCount }}
                                    </td>
                                    <td class="text-right">
                                        {{ $thread->lastPost->authorName }}
                                        <p class="text-muted">({{ $thread->lastPost->posted }})</p>
                                        <a href="{{ url( $thread->lastPostRoute ) }}" class="btn btn-primary btn-xs">{{ trans('forum::posts.view') }} &raquo;</a>
                                    </td>
                                @endif
                                @can ('manageThreads', $category)
                                    <td class="text-right">
                                        <input type="checkbox" name="items[]" value="{{ $thread->id }}">
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                {{ trans('forum::threads.none_found') }}
                            </td>
                            <td class="text-right" colspan="3">
                                @can ('createThreads', $category)
                                    <a href="{{ $category->newThreadRoute }}">{{ trans('forum::threads.post_the_first') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif

        @can ('manageThreads', $category)
                @include ('forum::category.partials.thread-actions')
            </form>
        @endcan

        <div class="row">
            <div class="col-xs-4">
                @can ('createThreads', $category)
                    <a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                @endcan
            </div>
            <div class="col-xs-8 text-right">
                {!! $category->threadsPaginated->render() !!}
            </div>
        </div>
    </div>
@stop
