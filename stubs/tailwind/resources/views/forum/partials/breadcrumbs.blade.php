<nav aria-label="breadcrumb">
    <ol class="flex [&_li]:after:content-['/'] [&_li]:after:px-2 [&_li]:after:text-gray-500 [&_li:last-child]:after:content-['']">
        <li class=""><a href="{{ url(config('forum.web.router.prefix')) }}" class="text-blue-500">{{ trans('forum::general.index') }}</a></li>
        @if (isset($category) && $category)
            @include ('forum.partials.breadcrumb-categories', ['category' => $category])
        @endif
        @if (isset($thread) && $thread)
            <li class=""><a href="{{ Forum::route('thread.show', $thread) }}" class="text-blue-500">{{ $thread->title }}</a></li>
        @endif
        @if (isset($breadcrumbs_append) && count($breadcrumbs_append) > 0)
            @foreach ($breadcrumbs_append as $breadcrumb)
                <li class="">{{ $breadcrumb }}</li>
            @endforeach
        @endif
    </ol>
</nav>
