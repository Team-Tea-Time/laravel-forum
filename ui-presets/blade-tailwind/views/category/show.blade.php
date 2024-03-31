{{-- $thread is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends('forum::layouts.main', ['thread' => null])

@section('content')
    <div class="flex flex-row justify-between mb-2">
        <h2 class="text-3xl" style="color: {{ $category->color_light_mode }};">
            {{ $category->title }} &nbsp;
            @if ($category->description)
                <small>{{ $category->description }}</small>
            @endif
        </h2>
    </div>

    <div id="category">
        <div class="flex justify-between flex-row-reverse">
            @if ($category->accepts_threads)
                @can ('createThreads', $category)
                    <x-forum::button-link href="{{ Forum::route('thread.create', $category) }}" class="float-end">{{ trans('forum::threads.new_thread') }}</x-forum::button-link>
                @endcan
            @endif

            <x-forum::button-group>
                @can ('editCategories')
                    <x-forum::button-secondary type="button" data-open-modal="edit-category">
                        {{ trans('forum::general.edit') }}
                    </x-forum::button-secondary>
                @endcan
            </x-forum::button-group>
        </div>

        @if (!$category->children->isEmpty())
            @foreach ($category->children as $subcategory)
                @include ('forum::category.partials.list', ['category' => $subcategory])
            @endforeach
        @endif

        @if ($category->accepts_threads)
            @if (!$threads->isEmpty())
                <div class="mt-4">
                    {{ $threads->links('forum::pagination') }}
                </div>

                @if (count($selectableThreadIds) > 0)
                    @can ('manageThreads', $category)
                        <form :action="actions[state.selectedAction]" method="POST">
                            @csrf
                            <input type="hidden" name="_method" :value="actionMethods[state.selectedAction]" />

                            <div class="text-end mt-2">
                                <div class="form-check">
                                    <label for="selectAllThreads">
                                        {{ trans('forum::threads.select_all') }}
                                    </label>
                                    <input type="checkbox" value="" id="selectAllThreads" class="align-middle" @click="toggleAll" :checked="state.selectedThreads.length == selectableThreadIds.length">
                                </div>
                            </div>
                    @endcan
                @endif

                <div class="threads list-group my-3 shadow-sm">
                    @foreach ($threads as $thread)
                        @include ('forum::thread.partials.list')
                    @endforeach
                </div>

                @if (count($selectableThreadIds) > 0)
                    @can ('manageThreads', $category)
                            <div class="fixed bottom-0 right-0 m-2" style="z-index: 1000;" v-if="state.selectedThreads.length">
                                <div class="bg-white shadow-sm rounded-md min-w-96 max-w-full">
                                    <div class="border-b text-center py-4 px-6">
                                        {{ trans('forum::general.with_selection') }}
                                    </div>
                                    <div class="p-6">
                                        <div class="mb-3">
                                            <div>
                                                <x-forum::label for="bulk-actions">{{ trans_choice('forum::general.actions', 1) }}</x-forum::label>
                                            </div>

                                            <x-forum::select id="bulk-actions" v-model="state.selectedAction">
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
                                            </x-forum::select>
                                        </div>

                                        <div class="mb-3" v-if="state.selectedAction == 'move'">
                                            <div>
                                                <label for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                                            </div>
                                            <x-forum::select name="category_id" id="category-id">
                                                @include ('forum::category.partials.options', [
                                                    'categories' => $threadDestinationCategories,
                                                    'hide' => $category
                                                ])
                                            </x-forum::select>
                                        </div>

                                        @if (config('forum.general.soft_deletes'))
                                            <div class="mb-3" v-if="state.selectedAction == 'delete'">
                                                <input class="form-check-input" type="checkbox" name="permadelete" value="1" id="permadelete">
                                                <label class="form-check-label" for="permadelete">
                                                    {{ trans('forum::general.perma_delete') }}
                                                </label>
                                            </div>
                                        @endif

                                        <div class="text-end">
                                            <button type="submit" class="bg-blue-500 text-white rounded-md px-3 py-1" @click="submit" :disabled="state.selectedAction == null">{{ trans('forum::general.proceed') }}</button>
                                        </div>
                                    </div>
                                </div>
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
                    {{ $threads->links('forum::pagination') }}
                </div>
                <div class="col col-xs-4 text-end">
                    @if ($category->accepts_threads)
                        @can ('createThreads', $category)
                            <x-forum::button-link href="{{ Forum::route('thread.create', $category) }}">{{ trans('forum::threads.new_thread') }}</x-forum::button-link>
                        @endcan
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if (!$threads->isEmpty())
        @can ('markThreadsAsRead')
            <div class="text-center mt-3">
                <x-forum::button class="inline-flex px-6 items-center gap-2" data-open-modal="mark-threads-as-read">
                    <i data-feather="book"></i> {{ trans('forum::general.mark_read') }}
                </x-forum::button>
            </div>

            @include ('forum::category.modals.mark-threads-as-read')
        @endcan
    @endif

    @can ('editCategories')
        @include ('forum::category.modals.edit')
    @endcan
    @can ('deleteCategories')
        @include ('forum::category.modals.delete')
    @endcan

    <script type="module">
    Vue.createApp({
        setup() {
            const selectableThreadIds = @json($selectableThreadIds);

            const actions = {
                delete: "{{ Forum::route('bulk.thread.delete') }}",
                restore: "{{ Forum::route('bulk.thread.restore') }}",
                lock: "{{ Forum::route('bulk.thread.lock') }}",
                unlock: "{{ Forum::route('bulk.thread.unlock') }}",
                pin: "{{ Forum::route('bulk.thread.pin') }}",
                unpin: "{{ Forum::route('bulk.thread.unpin') }}",
                move: "{{ Forum::route('bulk.thread.move') }}"
            };

            const actionMethods = {
                delete: 'DELETE',
                restore: 'POST',
                lock: 'POST',
                unlock: 'POST',
                pin: 'POST',
                unpin: 'POST',
                move: 'POST'
            };

            const state = Vue.reactive({
                selectedAction: null,
                selectedThreads: [],
                isEditModalOpen: false,
                isDeleteModalOpen: false
            });

            function toggleAll()
            {
                state.selectedThreads = (state.selectedThreads.length < selectableThreadIds.length) ? selectableThreadIds : [];
            }

            function submit(event)
            {
                if (actionMethods[state.selectedAction] === 'DELETE' && !confirm("{{ trans('forum::general.generic_confirm') }}"))
                {
                    event.preventDefault();
                }
            }

            function onClickModal(event)
            {
                if (event.target.classList.contains('modal'))
                {
                    state.isEditModalOpen = false;
                    state.isDeleteModalOpen = false;
                }
            }

            return {
                selectableThreadIds,
                actions,
                actionMethods,
                state,
                toggleAll,
                submit,
                onClickModal,
            };
        }
    }).mount('#category');
    </script>
@stop
