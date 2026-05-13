<article class="forum-panel forum-card-hover overflow-hidden" :class="{ 'ring-2 ring-cyan-400': typeof state !== 'undefined' && state.selectedThreads && state.selectedThreads.includes({{ $thread->id }}) }">
    <div class="grid gap-5 p-5 md:grid-cols-[1fr_auto_minmax(12rem,18rem)_auto] md:items-center md:p-6">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($thread->pinned)
                    <x-forum::badge type="info">{{ trans('forum::threads.pinned') }}</x-forum::badge>
                @endif
                @if ($thread->locked)
                    <x-forum::badge type="warning">{{ trans('forum::threads.locked') }}</x-forum::badge>
                @endif
                @if ($thread->userReadStatus !== null && !$thread->trashed())
                    <x-forum::badge type="success">{{ trans($thread->userReadStatus) }}</x-forum::badge>
                @endif
                @if ($thread->trashed())
                    <x-forum::badge type="danger">{{ trans('forum::general.deleted') }}</x-forum::badge>
                @endif
                @if (!$thread->isApproved)
                    <x-forum::badge type="warning">{{ trans('forum::general.pending_approval') }}</x-forum::badge>
                @endif
            </div>

            <a href="{{ Forum::route('thread.show', $thread) }}" class="mt-3 block text-lg font-extrabold leading-snug text-slate-950 no-underline hover:text-cyan-700 dark:text-white dark:hover:text-cyan-300">
                {{ $thread->title }}
            </a>

            <div class="forum-muted mt-2 flex flex-wrap items-center gap-x-2 gap-y-1">
                <span>{{ $thread->authorName }}</span>
                <span aria-hidden="true">/</span>
                @include ('forum::partials.timestamp', ['carbon' => $thread->created_at])

                @if (!isset($category))
                    <span aria-hidden="true">/</span>
                    <a href="{{ Forum::route('category.show', $thread->category) }}" class="font-bold no-underline" style="color: {{ $thread->category->color_light_mode }};">
                        {{ $thread->category->title }}
                    </a>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 md:justify-end">
            <x-forum::badge :style="(isset($category) && $category->color_light_mode) ? 'border-color: '.$category->color_light_mode .'; color: '.$category->color_light_mode .';' : null">
                {{ trans('forum::general.replies') }}: {{ $thread->reply_count }}
            </x-forum::badge>
        </div>

        @if ($thread->lastPost)
            <div class="forum-panel-soft px-4 py-3">
                <div class="forum-section-title">{{ trans_choice('forum::posts.post', 1) }}</div>
                <a href="{{ Forum::route('thread.show', $thread->lastPost) }}" class="mt-1 block truncate text-sm font-bold no-underline">
                    {{ trans('forum::posts.view') }}
                </a>
                <div class="forum-muted mt-1 truncate">
                    {{ $thread->lastPost->authorName }}
                    @include ('forum::partials.timestamp', ['carbon' => $thread->lastPost->created_at])
                </div>
            </div>
        @endif

        @if (isset($category) && isset($selectableThreadIds) && in_array($thread->id, $selectableThreadIds))
            <label class="flex items-center justify-end gap-2 text-sm font-bold text-slate-500 dark:text-slate-400">
                <span class="sr-only">Select</span>
                <input type="checkbox" name="threads[]" :value="{{ $thread->id }}" v-model="state.selectedThreads" class="forum-check">
            </label>
        @endif
    </div>
</article>
