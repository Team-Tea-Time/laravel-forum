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
                @can ('createThreads', $category)
                    <a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                @endcan
            </div>
            <div class="col-xs-8 text-right">
                {!! $threads->render() !!}
            </div>
        </div>

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
                    @if (!$threads->isEmpty())
                        @foreach ($threads as $thread)
                            <tr class="{{ $thread->trashed() ? "deleted" : "" }}">
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
                                @can ('manageThreads', $category)
                                    <td class="text-right">
                                        <input type="checkbox" name="threads[{{ $thread->id }}]">
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
            <div class="actions hidden">
                You can do stuff!
            </div>
        @endcan

        <div class="row">
            <div class="col-xs-4">
                @can ('createThreads', $category)
                    <a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                @endcan
            </div>
            <div class="col-xs-8 text-right">
                {!! $threads->render() !!}
            </div>
        </div>
    </div>

    <script>
    var toggle = $('input[type=checkbox][data-toggle-all]');
    var checkboxes = $('table tbody input[type=checkbox]');
    var actions = $('.actions');

    toggle.click(function() {
        checkboxes.prop('checked', toggle.is(':checked')).change();
    });

    checkboxes.change(function() {
        var tr = $(this).parents('tr');
        toggle.is(':checked') ? tr.addClass('active') : tr.removeClass('active');

        checkboxes.filter(':checked').length ? actions.removeClass('hidden') : actions.addClass('hidden');
    });
    </script>
@overwrite
