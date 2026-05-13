{{-- $thread is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends('forum::layouts.main', ['thread' => null])

@section('content')
    <section class="forum-hero">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="forum-hero-kicker">{{ trans_choice('forum::categories.category', 1) }}</div>
                <h1 class="forum-hero-title" style="color: {{ $category->color_light_mode }};">{{ $category->title }}</h1>
                @if ($category->description)
                    <p class="forum-hero-copy">{{ $category->description }}</p>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                @if ($category->accepts_threads)
                    @can ('createThreads', $category)
                        <x-forum::button-link href="{{ Forum::route('thread.create', $category) }}">
                            <i data-feather="plus" class="h-4 w-4"></i>
                            {{ trans('forum::threads.new_thread') }}
                        </x-forum::button-link>
                    @endcan
                @endif

                @can ('editCategories')
                    @can ('edit', $category)
                        <x-forum::button-secondary type="button" data-open-modal="edit-category">
                            <i data-feather="settings" class="h-4 w-4"></i>
                            {{ trans('forum::general.edit') }}
                        </x-forum::button-secondary>
                    @endcan
                @endcan
            </div>
        </div>
    </section>

    <div id="category" class="space-y-6">
        @if (!$category->children->isEmpty())
            <section class="space-y-4">
                <div class="forum-section-title">{{ trans_choice('forum::categories.category', 2) }}</div>
                @foreach ($category->children as $subcategory)
                    @include ('forum::category.partials.list', ['category' => $subcategory])
                @endforeach
            </section>
        @endif

        @if ($category->accepts_threads)
            <section class="space-y-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="forum-section-title">{{ trans_choice('forum::threads.thread', 2) }}</div>
                        <h2 class="mt-1">{{ trans_choice('forum::threads.thread', 2) }}</h2>
                    </div>

                    @can ('createThreads', $category)
                        <x-forum::button-link href="{{ Forum::route('thread.create', $category) }}">
                            <i data-feather="edit-3" class="h-4 w-4"></i>
                            {{ trans('forum::threads.new_thread') }}
                        </x-forum::button-link>
                    @endcan
                </div>

                @if (!$threads->isEmpty())
                    {{ $threads->links('forum::pagination') }}

                    @if (count($selectableThreadIds) > 0)
                        @can ('manageThreads', $category)
                            <form :action="actions[state.selectedAction]" method="POST">
                                @csrf
                                <input type="hidden" name="_method" :value="actionMethods[state.selectedAction]" />

                                <div class="flex justify-end">
                                    <label for="selectAllThreads" class="forum-panel-soft inline-flex cursor-pointer items-center gap-3 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300">
                                        <input type="checkbox" value="" id="selectAllThreads" class="forum-check" @click="toggleAll" :checked="state.selectedThreads.length == selectableThreadIds.length">
                                        {{ trans('forum::threads.select_all') }}
                                    </label>
                                </div>
                        @endcan
                    @endif

                    <div class="space-y-3">
                        @foreach ($threads as $thread)
                            @include ('forum::thread.partials.list')
                        @endforeach
                    </div>

                    @if (count($selectableThreadIds) > 0)
                        @can ('manageThreads', $category)
                                <div class="forum-action-bar" v-if="state.selectedThreads.length">
                                    <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <x-forum::label for="bulk-actions">{{ trans_choice('forum::general.actions', 1) }}</x-forum::label>
                                                <x-forum::select id="bulk-actions" v-model="state.selectedAction">
                                                    @if (Gate::allows('approveThreads') && Gate::allows('approveThreads', $category))
                                                        <option value="approve">{{ trans('forum::general.approve') }}</option>
                                                        <option value="unapprove">{{ trans('forum::general.unapprove') }}</option>
                                                    @endif
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

                                            <div v-if="state.selectedAction == 'move'">
                                                <x-forum::label for="category-id">{{ trans_choice('forum::categories.category', 1) }}</x-forum::label>
                                                <x-forum::select name="category_id" id="category-id">
                                                    @include ('forum::category.partials.options', [
                                                        'categories' => $threadDestinationCategories,
                                                        'hide' => $category
                                                    ])
                                                </x-forum::select>
                                            </div>

                                            @if (config('forum.general.soft_deletes'))
                                                <label class="flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-300" v-if="state.selectedAction == 'delete'">
                                                    <input class="forum-check" type="checkbox" name="permadelete" value="1" id="permadelete">
                                                    {{ trans('forum::general.perma_delete') }}
                                                </label>
                                            @endif
                                        </div>

                                        <x-forum::button type="submit" @click="submit" :disabled="state.selectedAction == null">{{ trans('forum::general.proceed') }}</x-forum::button>
                                    </div>
                                </div>
                            </form>
                        @endcan
                    @endif

                    {{ $threads->links('forum::pagination') }}
                @else
                    <div class="forum-empty">
                        {{ trans('forum::threads.none_found') }}
                        @can ('createThreads', $category)
                            <div class="mt-3">
                                <a href="{{ Forum::route('thread.create', $category) }}" class="font-bold">{{ trans('forum::threads.post_the_first') }}</a>
                            </div>
                        @endcan
                    </div>
                @endif
            </section>
        @endif
    </div>

    @if (!$threads->isEmpty())
        @can ('markThreadsAsRead')
            <div class="mt-8 flex justify-center">
                <x-forum::button class="px-6" data-open-modal="mark-threads-as-read">
                    <i data-feather="book-open" class="h-4 w-4"></i>
                    {{ trans('forum::general.mark_read') }}
                </x-forum::button>
            </div>

            @include ('forum::category.modals.mark-threads-as-read')
        @endcan
    @endif

    @can ('editCategories')
        @can ('edit', $category)
            @include ('forum::category.modals.edit')
        @endcan
    @endcan
    @can ('deleteCategories')
        @can ('delete', $category)
            @include ('forum::category.modals.delete')
        @endcan
    @endcan

    <script type="module">
    Vue.createApp({
        setup() {
            const selectableThreadIds = @json($selectableThreadIds);

            const actions = {
                approve: "{{ Forum::route('bulk.thread.approve') }}",
                unapprove: "{{ Forum::route('bulk.thread.unapprove') }}",
                delete: "{{ Forum::route('bulk.thread.delete') }}",
                restore: "{{ Forum::route('bulk.thread.restore') }}",
                lock: "{{ Forum::route('bulk.thread.lock') }}",
                unlock: "{{ Forum::route('bulk.thread.unlock') }}",
                pin: "{{ Forum::route('bulk.thread.pin') }}",
                unpin: "{{ Forum::route('bulk.thread.unpin') }}",
                move: "{{ Forum::route('bulk.thread.move') }}"
            };

            const actionMethods = {
                approve: 'POST',
                unapprove: 'POST',
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
