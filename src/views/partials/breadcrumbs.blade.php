<a href="{{ Config::get('forum::routes.root') }}">{{ trans('forum::base.index') }}</a>
@if (isset($parentCategory) && $parentCategory)
	&nbsp;&gt;&nbsp;<a href="{{{ $parentCategory->URL }}}">{{{ $parentCategory->title }}}</a>
@endif
@if (isset($category) && $category)
	&nbsp;&gt;&nbsp;<a href="{{{ $category->URL }}}">{{{ $category->title }}}</a>
@endif
@if (isset($thread) && $thread)
	&nbsp;&gt;&nbsp;<a href="{{{ $thread->URL }}}">{{{ $thread->title }}}</a>
@endif
