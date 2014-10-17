<a href="{{ Config::get('forum::routes.base') }}">{{ trans('forum::base.index') }}</a>
@if (isset($parentCategory) && $parentCategory)
	&nbsp;&gt;&nbsp;<a href="{{{ $parentCategory->url }}}">{{{ $parentCategory->title }}}</a>
@endif
@if (isset($category) && $category)
	&nbsp;&gt;&nbsp;<a href="{{{ $category->url }}}">{{{ $category->title }}}</a>
@endif
@if (isset($topic) && $topic)
	&nbsp;&gt;&nbsp;<a href="{{{ $topic->url }}}">{{{ $topic->title }}}</a>
@endif