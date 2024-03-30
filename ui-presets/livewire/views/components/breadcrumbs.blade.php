<nav class="breadcrumbs bg-slate-300 rounded-lg p-2 my-2 dark:bg-slate-700" aria-label="breadcrumb">
    <ol class="flex flex-wrap">
        <li>
            <a href="{{ url(config('forum.frontend.router.prefix')) }}" class="flex items-center">
                <span class="inline-block mr-1">
                    @include ('forum::components.icons.home-mini')
                </span>
                {{ trans('forum::general.index') }}
            </a>
        </li>
        @if (isset($category) && $category || isset($thread->category) && $thread->category)
            @include ('forum::components.breadcrumb-categories', ['category' => $category ?? $thread->category])
        @endif
        @if (isset($thread) && $thread)
            <li>{{ trans_choice('forum::threads.thread', 1) }}</li>
        @endif
        @if (isset($breadcrumbs_append))
            @foreach ($breadcrumbs_append as $breadcrumb)
                <li>{{ $breadcrumb }}</li>
            @endforeach
        @endif
    </ol>
</nav>
