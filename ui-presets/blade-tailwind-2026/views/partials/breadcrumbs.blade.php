<nav aria-label="breadcrumb" class="mb-5">
    <ol class="flex flex-wrap items-center gap-y-2 text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 [&_a]:text-slate-500 [&_a]:no-underline [&_a:hover]:text-slate-950 dark:[&_a]:text-slate-400 dark:[&_a:hover]:text-white [&_li]:after:content-['/'] [&_li]:after:px-2 [&_li]:after:text-slate-300 dark:[&_li]:after:text-slate-700 [&_li:last-child]:after:content-['']">
        <li><a href="{{ url(config('forum.frontend.router.prefix')) }}">{{ trans('forum::general.index') }}</a></li>
        @if (isset($category) && $category)
            @include ('forum::partials.breadcrumb-categories', ['category' => $category])
        @endif
        @if (isset($thread) && $thread)
            <li><a href="{{ Forum::route('thread.show', $thread) }}">{{ $thread->title }}</a></li>
        @endif
        @if (isset($breadcrumbs_append) && count($breadcrumbs_append) > 0)
            @foreach ($breadcrumbs_append as $breadcrumb)
                <li>{{ $breadcrumb }}</li>
            @endforeach
        @endif
    </ol>
</nav>
