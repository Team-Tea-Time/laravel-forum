@extends ('forum::layouts.main', ['thread' => null, 'breadcrumbs_append' => [$thread->title], 'thread_title' => $thread->title])

@section ('content')
    <div id="thread" class="space-y-6">
        <section class="forum-hero">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="forum-hero-kicker">
                        <a href="{{ Forum::route('category.show', $category) }}" class="no-underline" style="color: {{ $category->color_light_mode }};">{{ $category->title }}</a>
                    </div>
                    <h1 class="forum-hero-title">{{ $thread->title }}</h1>
                    <div class="forum-muted mt-4 flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span>{{ $thread->authorName }}</span>
                        <span aria-hidden="true">/</span>
                        @include ('forum::partials.timestamp', ['carbon' => $thread->created_at])
                        <span aria-hidden="true">/</span>
                        <span>{{ trans('forum::general.replies') }}: {{ $thread->reply_count }}</span>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @if ($thread->trashed())
                            <x-forum::badge type="danger">{{ trans('forum::general.deleted') }}</x-forum::badge>
                        @endif
                        @if ($thread->pinned)
                            <x-forum::badge type="info">{{ trans('forum::threads.pinned') }}</x-forum::badge>
                        @endif
                        @if ($thread->locked)
                            <x-forum::badge type="warning">{{ trans('forum::threads.locked') }}</x-forum::badge>
                        @endif
                        @if (!$thread->isApproved)
                            <x-forum::badge type="warning">{{ trans('forum::general.pending_approval') }}</x-forum::badge>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                    @if (!$thread->trashed())
                        @can ('reply', $thread)
                            <x-forum::button-link href="{{ Forum::route('post.create', $thread) }}">
                                <i data-feather="message-square" class="h-4 w-4"></i>
                                {{ trans('forum::general.new_reply') }}
                            </x-forum::button-link>
                            <x-forum::button-link href="#quick-reply" class="forum-button-secondary">
                                {{ trans('forum::general.quick_reply') }}
                            </x-forum::button-link>
                        @endcan
                    @endif
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-end gap-2">
                @if (Gate::allows('deleteThreads', $thread->category) && Gate::allows('delete', $thread))
                    @if ($thread->trashed())
                        <x-forum::button-link href="#" class="forum-button-danger" data-open-modal="perma-delete-thread">
                            <i data-feather="trash"></i> {{ trans('forum::general.perma_delete') }}
                        </x-forum::button-link>
                    @else
                        <x-forum::button-link href="#" class="forum-button-danger" data-open-modal="delete-thread">
                            <i data-feather="trash" class="w-4"></i> {{ trans('forum::general.delete') }}
                        </x-forum::button-link>
                    @endif
                @endif
                @if ($thread->trashed() && Gate::allows('restoreThreads', $thread->category) && Gate::allows('restore', $thread))
                    <x-forum::button-link href="#" data-open-modal="restore-thread" class="inline-flex items-center gap-2">
                        <i data-feather="refresh-cw" class="w-4"></i> {{ trans('forum::general.restore') }}
                    </x-forum::button-link>
                @endif

                @if (Gate::allows('lockThreads', $category)
                    || Gate::allows('pinThreads', $category)
                    || Gate::allows('rename', $thread)
                    || Gate::allows('moveThreadsFrom', $category)
                    || (Gate::allows('approveThreads') && Gate::allows('approveThreads', $category)))
                    <x-forum::button-group>
                        @if (!$thread->trashed())
                            @can ('lockThreads', $category)
                                @if ($thread->locked)
                                    <x-forum::button-link href="#" data-open-modal="unlock-thread" class="forum-button-secondary">
                                        <i data-feather="unlock" class="w-4"></i> {{ trans('forum::threads.unlock') }}
                                    </x-forum::button-link>
                                @else
                                    <x-forum::button-link href="#" data-open-modal="lock-thread" class="forum-button-secondary">
                                        <i data-feather="lock" class="w-4"></i> {{ trans('forum::threads.lock') }}
                                    </x-forum::button-link>
                                @endif
                            @endcan
                            @can ('pinThreads', $category)
                                @if ($thread->pinned)
                                    <x-forum::button-link href="#" data-open-modal="unpin-thread" class="forum-button-secondary">
                                        <i data-feather="arrow-down"></i> {{ trans('forum::threads.unpin') }}
                                    </x-forum::button-link>
                                @else
                                    <x-forum::button-link href="#" data-open-modal="pin-thread" class="forum-button-secondary">
                                        <i data-feather="arrow-up" class="w-4"></i> {{ trans('forum::threads.pin') }}
                                    </x-forum::button-link>
                                @endif
                            @endcan
                            @can ('rename', $thread)
                                <x-forum::button-link href="#"  data-open-modal="rename-thread" class="forum-button-secondary">
                                    <i data-feather="edit-2" class="w-4"></i> {{ trans('forum::general.rename') }}
                                </x-forum::button-link>
                            @endcan
                            @can ('moveThreadsFrom', $category)
                                <x-forum::button-link href="#" data-open-modal="move-thread" class="forum-button-secondary">
                                    <i data-feather="corner-up-right" class="w-4"></i> {{ trans('forum::general.move') }}
                                </x-forum::button-link>
                            @endcan
                            @if (Gate::allows('approveThreads') && Gate::allows('approveThreads', $category))
                                @if ($thread->isApproved)
                                    <x-forum::button-link href="#" data-open-modal="unapprove-thread" class="forum-button-secondary">
                                        <i data-feather="x-circle" class="w-4"></i> {{ trans('forum::general.unapprove') }}
                                    </x-forum::button-link>
                                @else
                                    <x-forum::button-link href="#" data-open-modal="approve-thread" class="forum-button-secondary">
                                        <i data-feather="check-circle" class="w-4"></i> {{ trans('forum::general.approve') }}
                                    </x-forum::button-link>
                                @endif
                            @endif
                        @endif
                    </x-forum::button-group>
                @endcan
        </div>

        @if ((count($posts) > 1 || $posts->currentPage() > 1) && (Gate::allows('deletePosts', $thread) || Gate::allows('restorePosts', $thread)) && count($selectablePosts) > 0)
            <form :action="postActions[state.selectedPostAction]" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="postActionMethods[state.selectedPostAction]" />
        @endif

        {{ $posts->links('forum::pagination') }}

        @if ((count($posts) > 1 || $posts->currentPage() > 1) && (Gate::allows('deletePosts', $thread) || Gate::allows('restorePosts', $thread)) && count($selectablePosts) > 0)
            <div class="flex justify-end">
                <label for="selectAllPosts" class="forum-panel-soft inline-flex cursor-pointer items-center gap-3 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300">
                    <input type="checkbox" value="" id="selectAllPosts" class="forum-check" @click="toggleAll" :checked="state.selectedPosts.length == posts.data.length">
                        {{ trans('forum::posts.select_all') }}
                </label>
            </div>
        @endif

        @foreach ($posts as $post)
            @include ('forum::post.partials.list', ['post' => $post, 'isSelectable' => in_array($post->id, $selectablePosts)])
        @endforeach

        @if (count($selectablePosts) > 0
            && ((Gate::allows('approvePosts') && Gate::allows('approvePosts', $thread))
                || Gate::allows('deletePosts', $thread)
                || Gate::allows('restorePosts', $thread)))
                <div class="forum-action-bar" v-if="state.selectedPosts.length">
                    <div class="grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
                        <div>
                            <x-forum::label for="bulk-actions">{{ trans('forum::general.with_selection') }}</x-forum::label>
                                <x-forum::select id="bulk-actions" v-model="state.selectedPostAction">
                                    @if (Gate::allows('approvePosts') && Gate::allows('approvePosts', $thread))
                                        <option value="approve">{{ trans('forum::general.approve') }}</option>
                                        <option value="unapprove">{{ trans('forum::general.unapprove') }}</option>
                                    @endif
                                    @can ('deletePosts', $thread)
                                        <option value="delete">{{ trans('forum::general.delete') }}</option>
                                    @endcan
                                    @can ('restorePosts', $thread)
                                        <option value="restore">{{ trans('forum::general.restore') }}</option>
                                    @endcan
                                </x-forum::select>

                            @if (config('forum.general.soft_deletes'))
                                <label class="mt-3 flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-300" v-if="state.selectedPostAction == 'delete'">
                                    <input class="forum-check" type="checkbox" name="permadelete" value="1" id="permadelete">
                                        {{ trans('forum::general.perma_delete') }}
                                </label>
                            @endif
                        </div>
                        <x-forum::button type="submit" @click="submitPosts">{{ trans('forum::general.proceed') }}</x-forum::button>
                    </div>
                </div>
            </form>
        @endif

        {{ $posts->links('forum::pagination') }}

        @if (!$thread->trashed())
            @can ('reply', $thread)
                <section id="quick-reply" class="forum-panel p-5 sm:p-6">
                    <div class="mb-5">
                        <div class="forum-section-title">{{ trans('forum::general.reply') }}</div>
                        <h2 class="mt-1">{{ trans('forum::general.quick_reply') }}</h2>
                    </div>
                    <form method="POST" action="{{ Forum::route('post.store', $thread) }}">
                        @csrf

                        <div>
                            <x-forum::textarea name="content" class="w-full min-h-48">{{ old('content') }}</x-forum::textarea>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <x-forum::button type="submit" class="px-5">{{ trans('forum::general.reply') }}</x-forum::button>
                        </div>
                    </form>
                </section>
            @endcan
        @endif
    </div>

    @if ($thread->trashed() && Gate::allows('restoreThreads', $thread->category) && Gate::allows('restore', $thread))
        @component('forum::modal-form')
            @slot('key', 'restore-thread')
            @slot('title', '<i data-feather="refresh-cw" class="text-gray-500"></i>' . trans('forum::general.restore'))
            @slot('route', Forum::route('thread.restore', $thread))
            @slot('method', 'POST')

            {{ trans('forum::general.generic_confirm') }}

            @slot('actions')
                <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
            @endslot
        @endcomponent
    @endif

    @if (Gate::allows('deleteThreads', $thread->category) && Gate::allows('delete', $thread))
        @component('forum::modal-form')
            @slot('key', 'delete-thread')
            @slot('title', '<i data-feather="trash" class="text-gray-500"></i>' . trans('forum::threads.delete'))
            @slot('route', Forum::route('thread.delete', $thread))
            @slot('method', 'DELETE')

            @if (config('forum.general.soft_deletes'))
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permadelete" value="1" id="permadelete">
                    <label class="form-check-label" for="permadelete">
                        {{ trans('forum::general.perma_delete') }}
                    </label>
                </div>
            @else
                {{ trans('forum::general.generic_confirm') }}
            @endif

            @slot('actions')
                <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
            @endslot
        @endcomponent

        @if (config('forum.general.soft_deletes'))
            @component('forum::modal-form')
                @slot('key', 'perma-delete-thread')
                @slot('title', '<i data-feather="trash" class="text-gray-500"></i>' . trans_choice('forum::threads.perma_delete', 1))
                @slot('route', Forum::route('thread.delete', $thread))
                @slot('method', 'DELETE')

                <input type="hidden" name="permadelete" value="1" />

                {{ trans('forum::general.generic_confirm') }}

                @slot('actions')
                    <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                @endslot
            @endcomponent
        @endif
    @endif

    @if (!$thread->trashed())
        @can ('lockThreads', $category)
            @if ($thread->locked)
                @component('forum::modal-form')
                    @slot('key', 'unlock-thread')
                    @slot('title', '<i data-feather="unlock" class="text-gray-500"></i> ' . trans('forum::threads.unlock'))
                    @slot('route', Forum::route('thread.unlock', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @else
                @component('forum::modal-form')
                    @slot('key', 'lock-thread')
                    @slot('title', '<i data-feather="lock" class="text-gray-500"></i> ' . trans('forum::threads.lock'))
                    @slot('route', Forum::route('thread.lock', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @endif
        @endcan

        @can ('pinThreads', $category)
            @if ($thread->pinned)
                @component('forum::modal-form')
                    @slot('key', 'unpin-thread')
                    @slot('title', '<i data-feather="arrow-down" class="text-gray-500"></i> ' . trans('forum::threads.unpin'))
                    @slot('route', Forum::route('thread.unpin', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @else
                @component('forum::modal-form')
                    @slot('key', 'pin-thread')
                    @slot('title', '<i data-feather="arrow-up" class="text-gray-500"></i> ' . trans('forum::threads.pin'))
                    @slot('route', Forum::route('thread.pin', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @endif
        @endcan

        @can ('rename', $thread)
            @component('forum::modal-form')
                @slot('key', 'rename-thread')
                @slot('title', '<i data-feather="edit-2" class="text-gray-500"></i> ' . trans('forum::general.rename'))
                @slot('route', Forum::route('thread.rename', $thread))
                @slot('method', 'POST')

                <div>
                    <x-forum::label for="new-title">{{ trans('forum::general.title') }}</x-forum::label>
                    <x-forum::input type="text" name="title" value="{{ $thread->title }}" class="w-full" />
                </div>

                @slot('actions')
                    <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                @endslot
            @endcomponent
        @endcan

        @can ('moveThreadsFrom', $category)
            @component('forum::modal-form')
                @slot('key', 'move-thread')
                @slot('title', '<i data-feather="corner-up-right" class="text-gray-500"></i> ' . trans('forum::general.move'))
                @slot('route', Forum::route('thread.move', $thread))
                @slot('method', 'POST')

                <div class="input-group">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                    </div>
                    <select name="category_id" id="category-id" class="form-select">
                        @include ('forum::category.partials.options', ['hide' => $thread->category])
                    </select>
                </div>

                @slot('actions')
                    <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                @endslot
            @endcomponent
        @endcan

        @if (Gate::allows('approveThreads') && Gate::allows('approveThreads', $category))
            @if ($thread->isApproved)
                @component('forum::modal-form')
                    @slot('key', 'unapprove-thread')
                    @slot('title', '<i data-feather="x-circle" class="text-gray-500"></i> ' . trans('forum::general.unapprove'))
                    @slot('route', Forum::route('thread.unapprove', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @else
                @component('forum::modal-form')
                    @slot('key', 'approve-thread')
                    @slot('title', '<i data-feather="check-circle" class="text-gray-500"></i> ' . trans('forum::general.approve'))
                    @slot('route', Forum::route('thread.approve', $thread))
                    @slot('method', 'POST')

                    {{ trans('forum::general.generic_confirm') }}

                    @slot('actions')
                        <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
                    @endslot
                @endcomponent
            @endif
        @endif
    @endif

    <script type="module">
    Vue.createApp({
        setup() {
            let posts = @json($posts);
            posts.data = posts.data.filter(post => post.sequence > 1);

            const selectablePosts = @json($selectablePosts);
            const postActions = {
                approve: "{{ Forum::route('bulk.post.approve') }}",
                unapprove: "{{ Forum::route('bulk.post.unapprove') }}",
                delete: "{{ Forum::route('bulk.post.delete') }}",
                restore: "{{ Forum::route('bulk.post.restore') }}"
            };
            const postActionMethods = {
                approve: 'POST',
                unapprove: 'POST',
                delete: 'DELETE',
                restore: 'POST',
            };

            const state = Vue.reactive({
                selectedPostAction: 'approve',
                selectedPosts: [],
                selectedThreadAction: null,
            });

            function toggleAll() {
                state.selectedPosts = (state.selectedPosts.length < selectablePosts.length) ? selectablePosts : [];
            }

            function submitThread(event) {
                if (threadActionMethods[state.selectedThreadAction] === 'DELETE' && !confirm("{{ trans('forum::general.generic_confirm') }}"))
                {
                    event.preventDefault();
                }
            }

            function submitPosts(event) {
                if (postActionMethods[state.selectedPostAction] === 'DELETE' && !confirm("{{ trans('forum::general.generic_confirm') }}")) {
                    event.preventDefault();
                }
            }

            return {
                posts,
                selectablePosts,
                postActions,
                postActionMethods,
                state,
                toggleAll,
                submitThread,
                submitPosts,
            };
        }
    }).mount('#thread');
    </script>
@stop
