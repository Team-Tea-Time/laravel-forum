<article class="forum-panel forum-card-hover overflow-hidden">
    <div class="grid gap-6 p-5 md:grid-cols-[1fr_auto_minmax(16rem,24rem)] md:items-center md:p-6">
        <div class="flex gap-4">
            <div class="mt-1 h-12 w-1.5 shrink-0 rounded-full" style="background: {{ $category->color_light_mode }};"></div>
            <div>
                <a href="{{ Forum::route('category.show', $category) }}" class="text-xl font-extrabold tracking-normal text-slate-950 no-underline hover:text-cyan-700 dark:text-white dark:hover:text-cyan-300">
                    {{ $category->title }}
                </a>
                @if ($category->description)
                    <p class="forum-muted mt-2">{{ $category->description }}</p>
                @endif
            </div>
        </div>

        @if ($category->accepts_threads)
            <div class="grid grid-cols-2 gap-2 sm:min-w-44">
                <div class="forum-panel-soft px-4 py-3 text-center">
                    <div class="text-xl font-black text-slate-950 dark:text-white">{{ $category->thread_count }}</div>
                    <div class="forum-section-title mt-1">{{ trans_choice('forum::threads.thread', 2) }}</div>
                </div>
                <div class="forum-panel-soft px-4 py-3 text-center">
                    <div class="text-xl font-black text-slate-950 dark:text-white">{{ $category->post_count }}</div>
                    <div class="forum-section-title mt-1">{{ trans_choice('forum::posts.post', 2) }}</div>
                </div>
            </div>
        @else
            <div class="hidden md:block"></div>
        @endif

        <div class="space-y-3">
            @if ($category->accepts_threads)
                @if ($category->newestThread)
                    <div>
                        <div class="forum-section-title">{{ trans_choice('forum::threads.thread', 1) }}</div>
                        <a href="{{ Forum::route('thread.show', $category->newestThread) }}" class="mt-1 block truncate text-sm font-bold no-underline">
                            {{ $category->newestThread->title }}
                        </a>
                        <div class="forum-muted mt-1">@include ('forum::partials.timestamp', ['carbon' => $category->newestThread->created_at])</div>
                    </div>
                @endif
                @if ($category->latestActiveThread && $category->latestActiveThread->post_count > 1)
                    <div class="border-t border-slate-200/70 pt-3 dark:border-white/10">
                        <div class="forum-section-title">{{ trans('forum::general.replies') }}</div>
                        <a href="{{ Forum::route('thread.show', $category->latestActiveThread->lastPost) }}" class="mt-1 block truncate text-sm font-bold no-underline">
                            Re: {{ $category->latestActiveThread->title }}
                        </a>
                        <div class="forum-muted mt-1">@include ('forum::partials.timestamp', ['carbon' => $category->latestActiveThread->lastPost->created_at])</div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @if ($category->children->count() > 0)
        <div class="border-t border-slate-200/70 bg-slate-50/55 dark:border-white/10 dark:bg-white/[0.025]">
            @foreach ($category->children as $subcategory)
                <div class="grid gap-4 border-b border-slate-200/60 p-5 last:border-b-0 dark:border-white/10 md:grid-cols-[1fr_auto_minmax(14rem,22rem)] md:items-center">
                    <div class="flex gap-3">
                        <div class="mt-1 h-9 w-1 shrink-0 rounded-full" style="background: {{ $subcategory->color_light_mode }};"></div>
                        <div>
                            <a href="{{ Forum::route('category.show', $subcategory) }}" class="font-bold text-slate-900 no-underline hover:text-cyan-700 dark:text-white dark:hover:text-cyan-300">
                                {{ $subcategory->title }}
                            </a>
                            <div class="forum-muted mt-1">{{ $subcategory->description }}</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <x-forum::badge>
                            {{ trans_choice('forum::threads.thread', 2) }}: {{ $subcategory->thread_count }}
                        </x-forum::badge>
                        <x-forum::badge>
                            {{ trans_choice('forum::posts.post', 2) }}: {{ $subcategory->post_count }}
                        </x-forum::badge>
                    </div>

                    <div>
                        @if ($subcategory->newestThread)
                            <a href="{{ Forum::route('thread.show', $subcategory->newestThread) }}" class="block truncate text-sm font-bold no-underline">{{ $subcategory->newestThread->title }}</a>
                            <div class="forum-muted mt-1">@include ('forum::partials.timestamp', ['carbon' => $subcategory->newestThread->created_at])</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</article>
