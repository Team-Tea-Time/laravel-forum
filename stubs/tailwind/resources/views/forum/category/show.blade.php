{{-- $thread is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends('forum.master', ['thread' => null])

@section('content')
    <div class="flex flex-row justify-between mb-2">
        <h2 class="text-3xl" style="color: {{ $category->color }};">
            {{ $category->title }} &nbsp;
            @if ($category->description)
                <small>{{ $category->description }}</small>
            @endif
        </h2>
    </div>

    <div class="v-category-show">
        <div class="flex justify-between flex-row-reverse">
            @if ($category->accepts_threads)
                @can ('createThreads', $category)
                    <x-forum.button-link href="{{ Forum::route('thread.create', $category) }}" class="btn btn-primary float-end">{{ trans('forum::threads.new_thread') }}</x-forum.button-link>
                @endcan
            @endif

            <x-forum.button-group>
                @can ('manageCategories')
                    <x-forum.button-secondary type="button" data-open-modal="edit-category">
                        {{ trans('forum::general.edit') }}
                    </x-forum.button-secondary>
                @endcan
            </x-forum.button-group>
        </div>

        @if (! $category->children->isEmpty())
            @foreach ($category->children as $subcategory)
                @include('forum.category.partials.list', ['category' => $subcategory])
            @endforeach
        @endif

        @if ($category->accepts_threads)
            @if (! $threads->isEmpty())
                <div class="mt-4">
                    {{ $threads->links('forum.pagination') }}
                </div>

                @if (count($selectableThreadIds) > 0)
                    @can ('manageThreads', $category)
                        <form :action="actions[selectedAction]" method="POST">
                            @csrf
                            <input type="hidden" name="_method" :value="actionMethods[selectedAction]" />

                            <div class="text-end mt-2">
                                <div class="form-check">
                                    <label for="selectAllThreads">
                                        {{ trans('forum::threads.select_all') }}
                                    </label>
                                    <input type="checkbox" value="" id="selectAllThreads" class="align-middle" @click="toggleAll" :checked="selectedThreads.length == selectableThreadIds.length">
                                </div>
                            </div>
                    @endcan
                @endif

                <div class="threads list-group my-3 shadow-sm">
                    @foreach ($threads as $thread)
                        @include ('forum.thread.partials.list')
                    @endforeach
                </div>

                @if (count($selectableThreadIds) > 0)
                    @can ('manageThreads', $category)
                            <div class="fixed bottom-0 right-0 m-2" style="z-index: 1000;">
                                <transition name="fade">
                                    <div class="bg-white shadow-sm rounded-md" v-if="selectedThreads.length">
                                        <div class="border-b text-center py-2 px-4">
                                            {{ trans('forum::general.with_selection') }}
                                        </div>
                                        <div class="p-4">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <x-forum.label for="bulk-actions">{{ trans_choice('forum::general.actions', 1) }}</x-forum.label>
                                                </div>
                                                <select class="form-select" id="bulk-actions" v-model="selectedAction">
                                                    @can ('deleteThreads', $category)
                                                        <option value="delete">{{ trans('forum::general.delete') }}</option>
                                                    @endcan
                                                    @can ('restoreThreads', $category)
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
                                                <x-forum.select name="category_id" id="category-id">
                                                    @include ('forum.category.partials.options', ['hide' => $category])
                                                </x-forum.select>
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
                                                <button type="submit" class="bg-blue-500 text-white rounded-md px-3 py-1" @click="submit" :disabled="selectedAction == null">{{ trans('forum::general.proceed') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </form>
                    @endcan
                @endif
            @else
                <div class="card my-3">
                    <div class="card-body">
                        {{ trans('forum::threads.none_found') }}
                        @can ('createThreads', $category)
                            <br>
                            <a href="{{ Forum::route('thread.create', $category) }}">{{ trans('forum::threads.post_the_first') }}</a>
                        @endcan
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col col-xs-8">
                    {{ $threads->links('forum.pagination') }}
                </div>
                <div class="col col-xs-4 text-end">
                    @if ($category->accepts_threads)
                        @can ('createThreads', $category)
                            <x-forum.button-link href="{{ Forum::route('thread.create', $category) }}">{{ trans('forum::threads.new_thread') }}</x-forum.button-link>
                        @endcan
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if (! $threads->isEmpty())
        @can ('markThreadsAsRead')
            <div class="text-center mt-3">
                <x-forum.button class="inline-flex px-6 items-center gap-2" data-open-modal="mark-threads-as-read">
                    <i data-feather="book"></i> {{ trans('forum::general.mark_read') }}
                </x-forum.button>
            </div>

            @include ('forum.category.modals.mark-threads-as-read')
        @endcan
    @endif

    @can ('manageCategories')
        @include ('forum.category.modals.edit')
        @include ('forum.category.modals.delete')
    @endcan

    <style>
    .list-group.threads .list-group-item
    {
        border-left-width: 2px;
    }

    .list-group.threads .list-group-item.locked
    {
        border-left-color: var(--bs-yellow);
    }

    .list-group.threads .list-group-item.pinned
    {
        border-left-color: var(--bs-cyan);
    }

    .list-group.threads .list-group-item.deleted
    {
        border-left-color: var(--bs-red);
        opacity: 0.5;
    }
    </style>

    <script>
    new Vue({
        el: '.v-category-show',
        name: 'CategoryShow',
        data: {
            selectableThreadIds: @json($selectableThreadIds),
            actions: {
                'delete': "{{ Forum::route('bulk.thread.delete') }}",
                'restore': "{{ Forum::route('bulk.thread.restore') }}",
                'lock': "{{ Forum::route('bulk.thread.lock') }}",
                'unlock': "{{ Forum::route('bulk.thread.unlock') }}",
                'pin': "{{ Forum::route('bulk.thread.pin') }}",
                'unpin': "{{ Forum::route('bulk.thread.unpin') }}",
                'move': "{{ Forum::route('bulk.thread.move') }}"
            },
            actionMethods: {
                'delete': 'DELETE',
                'restore': 'POST',
                'lock': 'POST',
                'unlock': 'POST',
                'pin': 'POST',
                'unpin': 'POST',
                'move': 'POST'
            },
            selectedAction: null,
            selectedThreads: [],
            isEditModalOpen: false,
            isDeleteModalOpen: false
        },
        methods: {
            toggleAll ()
            {
                this.selectedThreads = (this.selectedThreads.length < this.selectableThreadIds.length) ? this.selectableThreadIds : [];
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
