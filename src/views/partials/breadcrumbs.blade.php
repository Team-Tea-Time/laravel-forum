<a href="{{ Config::get('forum::routes.root') }}">{{ trans('forum::base.index') }}</a>
@if (isset($parentCategory) && $parentCategory)
	&nbsp;&gt;&nbsp;<a href="{{{ $parentCategory->url }}}">{{{ $parentCategory->title }}}</a>
@endif
@if (isset($category) && $category)
	&nbsp;&gt;&nbsp;<a href="{{{ $category->url }}}">{{{ $category->title }}}</a>
@endif
@if (isset($thread) && $thread)
	&nbsp;&gt;&nbsp;<a href="{{{ $thread->url }}}">{{{ $thread->title }}}</a>
@endif
