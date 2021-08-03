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
                <a href="{{ Forum::route('thread.create', $category) }}" class="btn btn-primary float-end">{{ trans('forum::threads.new_thread') }}</a>
            @endcan
        @endif

        <div class="btn-group" role="group">
            @can('manageCategories')
                <button type="button" class="btn btn-secondary" data-open-modal="edit-category">
                    {{ trans('forum::general.edit') }}
                </button>
            @endcan
        </div>

        @if (! $category->children->isEmpty())
            @foreach ($category->children as $subcategory)
                @include('forum::category.partials.list', ['category' => $subcategory])
            @endforeach
        @endif

        @if ($category->accepts_threads)
            @if (! $threads->isEmpty())
                {{ $threads->links() }}

                @can('manageThreads', $category)
                    <form :action="actions[selectedAction]" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="actionMethods[selectedAction]" />

                        <div class="text-end mt-2">
                            <div class="form-check">
                                <label for="selectAllThreads">
                                    {{ trans('forum::threads.select_all') }}
                                </label>
                                <input type="checkbox" value="" id="selectAllThreads" class="align-middle" @click="toggleAll" :checked="selectedThreads.length == threads.data.length">
                            </div>
                        </div>
                @endcan

                <div class="threads list-group my-3 shadow-sm">
                    @foreach ($threads as $thread)
                        @include ('forum::thread.partials.list')
                    @endforeach
                </div>

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

                                        <div class="mb-3" v-if="selectedAction == 'move'">
                                            <label for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                                            <select name="category_id" id="category-id" class="form-control">
                                                @include ('forum::category.partials.options', ['hide' => $category])
                                            </select>
                                        </div>

                                        @if (config('forum.general.soft_deletes'))
                                            <div class="form-check mb-3" v-if="selectedAction == 'delete'">
                                                <input class="form-check-input" type="checkbox" name="permadelete" value="1" id="permadelete">
                                                <label class="form-check-label" for="permadelete">
                                                    {{ trans('forum::general.perma_delete') }}
                                                </label>
                                            </div>
                                        @endif

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary" @click="submit">{{ trans('forum::general.proceed') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </form>
                @endcan
            @else
                <div class="card my-3">
                    <div class="card-body">
                        {{ trans('forum::threads.none_found') }}
                        @can('createThreads', $category)
                            <br>
                            <a href="{{ Forum::route('thread.create', $category) }}">{{ trans('forum::threads.post_the_first') }}</a>
                        @endcan
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col col-xs-8">
                    {{ $threads->links() }}
                </div>
                <div class="col col-xs-4 text-end">
                    @if ($category->accepts_threads)
                        @can('createThreads', $category)
                            <a href="{{ Forum::route('thread.create', $category) }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                        @endcan
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if (! $threads->isEmpty())
        @can ('markThreadsAsRead')
            <div class="text-center">
                <button class="btn btn-primary px-5" data-open-modal="mark-threads-as-read">
                    <i data-feather="book"></i> {{ trans('forum::general.mark_read') }}
                </button>
            </div>

            @include ('forum::category.modals.mark-threads-as-read')
        @endcan
    @endif

    @can('manageCategories')
        @include ('forum::category.modals.edit')
        @include ('forum::category.modals.delete')
    @endcan

    <style>
    .list-group.threads .list-group-item
    {
        border-left-width: 2px;
    }

    .list-group.threads .list-group-item.locked
    {
        border-left-color: var(--yellow);
    }

    .list-group.threads .list-group-item.pinned
    {
        border-left-color: var(--cyan);
    }

    .list-group.threads .list-group-item.deleted
    {
        border-left-color: var(--red);
        opacity: 0.5;
    }
    </style>

    <script>
    new Vue({
        el: '.v-category-show',
        name: 'CategoryShow',
        data: {
            threads: @json($threads),
            actions: {
                'delete': "{{ Forum::route('bulk.thread.delete') }}",
                'restore': "{{ Forum::route('bulk.thread.restore') }}",
                'lock': "{{ Forum::route('bulk.thread.lock') }}",
                'unlock': "{{ Forum::route('bulk.thread.unlock') }}",
                'pin': "{{ Forum::route('bulk.thread.pin') }}",
                'unpin': "{{ Forum::route('bulk.thread.unpin') }}"
            },
            actionMethods: {
                'delete': 'DELETE',
                'restore': 'POST',
                'lock': 'POST',
                'unlock': 'POST',
                'pin': 'POST',
                'unpin': 'POST'
            },
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
                if (this.actionMethods[this.selectedAction] === 'DELETE' && ! confirm("{{ trans('forum::general.generic_confirm') }}"))
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
