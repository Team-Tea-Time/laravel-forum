<aside class="mb-5 rounded-lg border-l-4 border-cyan-400 bg-cyan-50/70 p-4 dark:bg-cyan-400/10">
    <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
        <div class="text-sm font-bold text-slate-700 dark:text-slate-200">
            {{ $post->authorName }}
            <span class="font-medium text-slate-500 dark:text-slate-400">{{ $post->posted }}</span>
        </div>
        <a href="{{ Forum::route('thread.show', $post) }}" class="text-xs font-bold no-underline text-slate-500 dark:text-slate-400">#{{ $post->sequence }}</a>
    </div>
    <div class="line-clamp-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
        {!! \Illuminate\Support\Str::limit(Forum::render($post->content), 260) !!}
    </div>
</aside>
