<ol class="breadcrumb">
    <li><a href="{{ url(config('forum.routing.root')) }}">{{ trans('forum::general.index') }}</a></li>
    @if (isset($category) && $category)
        @include ('forum::partials.breadcrumb-categories', ['category' => $category])
    @endif
    @if (isset($thread) && $thread)
        <li><a href="{{ $thread->route }}">{{ $thread->title }}</a></li>
    @endif
    @if (isset($breadcrumb_other) && $breadcrumb_other)
        <li>{!! $breadcrumb_other !!}</li>
    @endif
</ol>
