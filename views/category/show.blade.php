{{-- $thread is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends('forum::master', ['thread' => null])

@section('content')
    <div class="d-flex flex-row justify-content-between mb-2">
        <h2 style="color: {{ $category->color }};">
            {{ $category->title }} &nbsp;
            @if ($category->description)
                <small>{{ $category->description }}</small>
            @endif
        </h2>
    </div>
    <div class="v-category-show">
        @if ($category->accepts_threads)
            @can('createThreads', $category)
                <a href="{{ Forum::route('thread.create', $category) }}" class="btn btn-primary float-right">{{ trans('forum::threads.new_thread') }}</a>
            @endcan
        @endif
        <div class="btn-group" role="group">
            @can('manageCategories')
                @include ('forum::category.partials.actions.edit')
            @endcan
        </div>

        @if (! $category->children->isEmpty())
            @foreach ($category->children as $subcategory)
                @include('forum::category.partials.list', ['category' => $subcategory])
            @endforeach
        @endif

        {{ $threads->links() }}

        @can('manageThreads', $category)
            <form action="{{ Forum::route('bulk.thread.update') }}" method="POST" data-actions-form>
                {!! csrf_field() !!}
                {!! method_field('delete') !!}
        @endcan

        @if ($category->accepts_threads)
            <table class="table table-thread mt-3">
                <thead>
                    <tr>
                        <th>{{ trans('forum::general.subject') }}</th>
                        <th class="col col-md-2 text-right">{{ trans('forum::general.replies') }}</th>
                        <th class="col col-md-2 text-right">{{ trans('forum::posts.last') }}</th>
                        @can('manageThreads', $category)
                            <th class="col col-md-1 text-right"><input type="checkbox" data-toggle-all></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @if (!$threads->isEmpty())
                        @foreach ($threads as $thread)
                            <tr class="{{ $thread->trashed() ? "deleted" : "" }}">
                                <td>
                                    <span class="lead">
                                        @if ($thread->locked)
                                            <span class="badge badge-warning">{{ trans('forum::threads.locked') }}</span>
                                        @endif
                                        @if ($thread->pinned)
                                            <span class="badge badge-info">{{ trans('forum::threads.pinned') }}</span>
                                        @endif
                                        @if ($thread->userReadStatus && !$thread->trashed())
                                            <span class="badge badge-primary">{{ trans($thread->userReadStatus) }}</span>
                                        @endif
                                        @if ($thread->trashed())
                                            <span class="badge badge-danger">{{ trans('forum::general.deleted') }}</span>
                                        @endif
                                        <a href="{{ Forum::route('thread.show', $thread) }}">{{ $thread->title }}</a>
                                    </span>
                                    <br>
                                    {{ $thread->authorName }} <span class="text-muted">({{ $thread->posted }})</span>
                                </td>
                                @if ($thread->trashed())
                                    <td colspan="2">&nbsp;</td>
                                @else
                                    <td class="text-right">
                                        {{ $thread->reply_count }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ Forum::route('thread.show', $thread->lastPost) }}">{{ trans('forum::posts.view') }} &raquo;</a>
                                        <br>
                                        {{ $thread->lastPost->authorName }}
                                        <span class="text-muted">({{ $thread->lastPost->posted }})</span>
                                    </td>
                                @endif
                                @can('manageThreads', $category)
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
                                @can('createThreads', $category)
                                    <a href="{{ Forum::route('thread.create', $category) }}">{{ trans('forum::threads.post_the_first') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif

        @can('manageThreads', $category)
                @include('forum::category.partials.thread-actions')
            </form>
        @endcan

        <div class="row">
            <div class="col col-xs-8">
                {{ $threads->links() }}
            </div>
            <div class="col col-xs-4 text-right">
                @if ($category->accepts_threads)
                    @can('createThreads', $category)
                        <a href="{{ Forum::route('thread.create', $category) }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <script>

    new Vue({
        el: '.v-category-show',
        data: {
            isEditModalOpen: false,
            isDeleteModalOpen: false
        },
        methods: {
            onClickModal (event)
            {
                if (event.target.classList.contains('modal'))
                {
                    this.isEditModalOpen = false;
                    this.isDeleteModalOpen = false;
                }
            }
        }
    })
    </script>
@stop
