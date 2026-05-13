<article @if (!$post->trashed())id="post-{{ $post->sequence }}"@endif
    class="forum-panel mb-4 overflow-hidden {{ $post->trashed() || $thread->trashed() ? 'opacity-60' : '' }}"
    :class="{ 'ring-2 ring-cyan-400': typeof state !== 'undefined' && state.selectedPosts && state.selectedPosts.includes({{ $post->id }}) }">
    <header class="flex flex-col gap-4 border-b border-slate-200/70 bg-slate-50/70 px-5 py-4 dark:border-white/10 dark:bg-white/[0.035] sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="grid h-10 w-10 place-items-center rounded-full bg-slate-950 text-sm font-black uppercase text-white dark:bg-white dark:text-slate-950">
                {{ mb_substr($post->authorName, 0, 1) }}
            </div>
            <div>
                <div class="font-bold text-slate-950 dark:text-white">{{ $post->authorName }}</div>
                <div class="forum-muted">
                    @include ('forum::partials.timestamp', ['carbon' => $post->created_at])
                    @if ($post->hasBeenUpdated())
                        <span>({{ trans('forum::general.last_updated') }} @include ('forum::partials.timestamp', ['carbon' => $post->updated_at]))</span>
                    @endif
                </div>
            </div>
        </div>

        @if (!isset($single) || !$single)
            <div class="flex items-center gap-3">
                <a href="{{ Forum::route('thread.show', $post) }}" class="rounded-full bg-white px-3 py-1 text-sm font-bold no-underline shadow-sm dark:bg-white/10">
                    #{{ $post->sequence }}
                </a>
                @if ($isSelectable)
                    <input type="checkbox" name="posts[]" :value="{{ $post->id }}" v-model="state.selectedPosts" class="forum-check" />
                @endif
            </div>
        @endif
    </header>

    <div class="p-5 sm:p-6">
        @if ($post->parent !== null)
            @include ('forum::post.partials.quote', ['post' => $post->parent])
        @endif

        @if ($post->sequence != 1 && $post->thread->category->requiresPostApproval() && !$post->isApproved)
            <div class="mb-4">
                <x-forum::badge type="warning">{{ trans('forum::general.pending_approval') }}</x-forum::badge>
            </div>
        @endif

        <div class="forum-prose">
            @if ($post->trashed())
                @can ('viewTrashedPosts')
                    {!! Forum::render($post->content) !!}
                @endcan
                <div class="mt-4">
                    <x-forum::badge type="danger">{{ trans('forum::general.deleted') }}</x-forum::badge>
                </div>
            @else
                {!! Forum::render($post->content) !!}
            @endif
        </div>

        @if (!isset($single) || !$single)
            <footer class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200/70 pt-4 text-sm dark:border-white/10">
                @if (!$post->trashed())
                    <a href="{{ Forum::route('post.show', $post) }}" class="font-bold no-underline text-slate-500 hover:text-slate-950 dark:text-slate-400 dark:hover:text-white">{{ trans('forum::general.permalink') }}</a>
                    @if ($post->sequence != 1)
                        @can ('deletePosts', $post->thread)
                            @can ('delete', $post)
                                <a href="{{ Forum::route('post.confirm-delete', $post) }}" class="font-bold no-underline text-rose-600 hover:text-rose-500">{{ trans('forum::general.delete') }}</a>
                            @endcan
                        @endcan
                    @endif
                    @can ('edit', $post)
                        <a href="{{ Forum::route('post.edit', $post) }}" class="font-bold no-underline">{{ trans('forum::general.edit') }}</a>
                    @endcan
                    @can ('reply', $post->thread)
                        <a href="{{ Forum::route('post.create', $post) }}" class="font-bold no-underline">{{ trans('forum::general.reply') }}</a>
                    @endcan
                @else
                    @can ('restorePosts', $post->thread)
                        @can ('restore', $post)
                            <a href="{{ Forum::route('post.confirm-restore', $post) }}" class="font-bold no-underline">{{ trans('forum::general.restore') }}</a>
                        @endcan
                    @endcan
                @endif
            </footer>
        @endif
    </div>
</article>
