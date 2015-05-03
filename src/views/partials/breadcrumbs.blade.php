<ol class="breadcrumb">
    <li><a href="{{ config('forum.routing.root') }}">{{ trans('forum::base.index') }}</a></li>
    @if (isset($parentCategory) && $parentCategory)
        <li><a href="{!! $parentCategory->route !!}">{!! $parentCategory->title !!}</a></li>
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
