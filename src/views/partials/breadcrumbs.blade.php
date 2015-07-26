<ol class="breadcrumb">
    <li><a href="{{ config('forum.routing.root') }}">{{ trans('forum::general.index') }}</a></li>
    @if (isset($category) && $category)
        @include ('forum::partials.breadcrumb-categories', ['category' => $category])
    @endif
    @if (isset($thread) && $thread)
        <li><a href="{!! $thread->route !!}">{!! $thread->title !!}</a></li>
    @endif
    @if (isset($other) && $other)
        <li>{!! $other !!}</li>
    @endif
</ol>
