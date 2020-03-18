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
            <form action="{{ Forum::route('bulk.thread.update') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="actionMethods[selectedAction]" />

                <div class="text-right mt-2">
                    <div class="form-check">
                        <label for="selectAllThreads">
                            {{ trans('forum::threads.select_all') }}
                        </label>
                        <input type="checkbox" value="" id="selectAllThreads" @click="toggleAll" :checked="selectedThreads.length == threads.data.length">
                    </div>
                </div>
        @endcan

        @if ($category->accepts_threads)
            @if (! $threads->isEmpty())
                <div class="thread list-group my-2 shadow-sm">
                @foreach ($threads as $thread)
                    <div class="list-group-item" :class="{ 'border-primary': selectedThreads.includes({{ $thread->id }}) }" style="z-index: 1;">
                        <div class="row align-items-center text-center">
                            <div class="col-sm text-md-left">
                                <span class="lead">
                                    <a href="{{ Forum::route('thread.show', $thread) }}" style="color: {{ $category->color }};">{{ $thread->title }}</a>
                                </span>
                                <br>
                                {{ $thread->authorName }} <span class="text-muted">({{ $thread->posted }})</span>
                            </div>
                            <div class="col-sm text-md-right">
                                @if ($thread->locked)
                                    <span class="badge badge-pill badge-warning">{{ trans('forum::threads.locked') }}</span>
                                @endif
                                @if ($thread->pinned)
                                    <span class="badge badge-pill badge-info">{{ trans('forum::threads.pinned') }}</span>
                                @endif
                                @if ($thread->userReadStatus && !$thread->trashed())
                                    <span class="badge badge-pill badge-primary">{{ trans($thread->userReadStatus) }}</span>
                                @endif
                                @if ($thread->trashed())
                                    <span class="badge badge-pill badge-danger">{{ trans('forum::general.deleted') }}</span>
                                @endif
                                <span class="badge badge-pill badge-primary" style="background: {{ $category->color }};">
                                    {{ trans('forum::general.replies') }}: 
                                    {{ $thread->reply_count }}
                                </span>
                            </div>
                            <div class="col-sm text-md-right text-muted">
                                <a href="{{ Forum::route('thread.show', $thread->lastPost) }}">{{ trans('forum::posts.view') }} &raquo;</a>
                                <br>
                                {{ $thread->lastPost->authorName }}
                                <span class="text-muted">({{ $thread->lastPost->posted }})</span>
                            </div>
                            @can('manageThreads', $category)
                                <div class="col-sm" style="flex: 0;">
                                    <input type="checkbox" :value="{{ $thread->id }}" v-model="selectedThreads">
                                </div>
                            @endcan
                        </div>
                    </div>
                @endforeach
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        {{ trans('forum::threads.none_found') }}
                        @can('createThreads', $category)
                            <br>
                            <a href="{{ Forum::route('thread.create', $category) }}">{{ trans('forum::threads.post_the_first') }}</a>
                        @endcan
                    </div>
                </div>
            @endif
        @endif

        @can('manageThreads', $category)
                <div class="fixed-bottom-right pb-xs-0 pr-xs-0 pb-sm-3 pr-sm-3" style="z-index: 1000;">
                    <transition name="fade">
                        <div class="card text-white bg-secondary shadow-sm" v-if="selectedThreads.length">
                            <div class="card-header text-center">
                                {{ trans('forum::general.with_selection') }}
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="bulk-actions">{{ trans_choice('forum::general.actions', 1) }}</label>
                                    </div>
                                    <select class="custom-select" id="bulk-actions" v-model="selectedAction">
                                        @can ('deleteThreads', $category)
                                            <option value="delete">{{ trans('forum::general.delete') }}</option>
                                            <option value="restore">{{ trans('forum::general.restore') }}</option>
                                            <option value="permadelete">{{ trans('forum::general.perma_delete') }}</option>
                                        @endcan
                                        @can ('moveThreadsFrom', $category)
                                            <option value="move">{{ trans('forum::general.move') }}</option>
                                        @endcan
                                        @can ('lockThreads', $category)
                                            <option value="lock">{{ trans('forum::threads.lock') }}</option>
                                            <option value="unlock">{{ trans('forum::threads.unlock') }}</option>
                                        @endcan
                                        @can ('pinThreads', $category)
                                            <option value="pin">{{ trans('forum::threads.pin') }}</option>
                                            <option value="unpin">{{ trans('forum::threads.unpin') }}</option>
                                        @endcan
                                    </select>
                                </div>

                                <div class="form-group" v-if="selectedAction == 'move'">
                                    <label for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                                    <select name="category_id" id="category-id" class="form-control">
                                        @include ('forum::category.partials.options', ['hide' => $category])
                                    </select>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" @click="submit">{{ trans('forum::general.proceed') }}</button>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
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
        name: 'CategoryShow',
        data: {
            threads: @json($threads),
            actionMethods: {
                'delete': 'DELETE',
                'permadelete': 'DELETE',
                'restore': 'PATCH',
                'lock': 'PATCH',
                'unlock': 'PATCH',
                'pin': 'PATCH',
                'unpin': 'PATCH'
            },
            actionsRequiringConfirmation: ['delete', 'permadelete'],
            selectedAction: 'delete',
            selectedThreads: [],
            isEditModalOpen: false,
            isDeleteModalOpen: false
        },
        computed: {
            threadIds ()
            {
                return this.threads.data.map(thread => thread.id);
            }
        },
        methods: {
            toggleAll ()
            {
                this.selectedThreads = (this.selectedThreads.length < this.threads.data.length) ? this.threadIds : [];
            },
            submit (event)
            {
                if (this.actionsRequiringConfirmation.includes(this.selectedAction) && ! confirm("{{ trans('forum::general.generic_confirm') }}"))
                {
                    event.preventDefault();
                }
            },
            onClickModal (event)
            {
                if (event.target.classList.contains('modal'))
                {
                    this.isEditModalOpen = false;
                    this.isDeleteModalOpen = false;
                }
            }
        }
    });
    </script>
@stop
