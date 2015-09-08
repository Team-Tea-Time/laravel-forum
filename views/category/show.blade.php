{{--
    $thread is passed as NULL to the master layout view to prevent it from
    showing in the breadcrumbs
--}}
@extends ('forum::master', ['thread' => null])

@section ('content')
    <div id="category">
        <h2>{{ $category->title }}</h2>

        @if (!$category->children->isEmpty())
            <table class="table table-category">
                <thead>
                    <tr>
                        <th>{{ trans_choice('forum::categories.category', 1) }}</th>
                        <th class="col-md-2">{{ trans_choice('forum::threads.thread', 2) }}</th>
                        <th class="col-md-2">{{ trans_choice('forum::posts.post', 2) }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($category->children as $subcategory)
                        @include ('forum::category.partials.list')
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="row">
            <div class="col-xs-4">
                @if (Forum::userCan('thread.create', compact('category')))
                    <a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                @endif
            </div>
            <div class="col-xs-8 text-right">
                {!! $category->pageLinks !!}
            </div>
        </div>

        @if ($category->threadsAllowed)
            <table class="table table-thread">
                <thead>
                    <tr>
                        <th>{{ trans('forum::general.subject') }}</th>
                        <th class="col-md-2 text-right">{{ trans('forum::general.replies') }}</th>
                        <th class="col-md-2 text-right">{{ trans('forum::posts.last') }}</th>
                        @if (Forum::userCan(['api.bulk.thread.destroy', 'api.bulk.thread.update', 'api.bulk.thread.restore'], compact('category')))
                            <th class="col-md-1 text-right"><input type="checkbox" data-toggle-all></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if (!$threads->isEmpty())
                        @foreach ($threads as $thread)
                            <tr class="{{ ($thread->trashed()) ? "deleted" : "" }}">
                                <td>
                                    <span class="pull-right">
                                        @if ($thread->locked)
                                            <span class="label label-danger">{{ trans('forum::threads.locked') }}</span>
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
                                @if (Forum::userCan(['api.bulk.thread.destroy', 'api.bulk.thread.update', 'api.bulk.thread.restore'], compact('category')))
                                    <td class="text-right">
                                        <input type="checkbox" name="threads[{{ $thread->id }}]">
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                {{ trans('forum::threads.none_found') }}
                            </td>
                            <td class="text-right" colspan="3">
                                @if ($category->userCanCreateThreads)
                                    <a href="{{ $category->newThreadRoute }}">{{ trans('forum::threads.post_the_first') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif

        @if (Forum::userCan(['api.bulk.thread.destroy', 'api.bulk.thread.update', 'api.bulk.thread.restore'], compact('category')))
            <div class="actions hidden">
                You can do stuff!
            </div>
        @endif

        <div class="row">
            <div class="col-xs-4">
                @if (Forum::userCan('thread.create', compact('category')))
                    <a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                @endif
            </div>
            <div class="col-xs-8 text-right">
                {!! $category->pageLinks !!}
            </div>
        </div>
    </div>

    <script>
    $('input[type=checkbox][data-toggle-all]').click(function() {
        var checkboxes = $('table tbody input[type=checkbox]');
        var actions = $('.actions');

        checkboxes.prop('checked', $(this).is(':checked')).change();

        checkboxes.change(function() {
            var tr = $(this).parents('tr');
            $(this).is(':checked') ? tr.addClass('active') : tr.removeClass('active');
            
            checkboxes.filter(':checked').length ? actions.removeClass('hidden') : actions.addClass('hidden');
        });
    });
    </script>
@overwrite
