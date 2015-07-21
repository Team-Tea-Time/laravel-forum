<ol class="breadcrumb">
    <li><a href="{{ config('forum.routing.root') }}">{{ trans('forum::general.index') }}</a></li>
    @if (isset($category) && !is_null($category->parent))
        <li><a href="{!! $category->parent->route !!}">{!! $category->parent->title !!}</a></li>
    @endif
    @if (isset($category) && $category)
        <li><a href="{!! $category->route !!}">{!! $category->title !!}</a></li>
    @endif
    @if (isset($thread) && $thread)
        <li><a href="{!! $thread->route !!}">{!! $thread->title !!}</a></li>
    @endif
    @if (isset($other) && $other)
        <li>{!! $other !!}</li>
    @endif
</ol>
