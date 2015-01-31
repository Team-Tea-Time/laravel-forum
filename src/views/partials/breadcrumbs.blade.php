<ol class="breadcrumb">
  <li><a href="{{ Config::get('forum::routes.root') }}">{{ trans('forum::base.index') }}</a></li>
	@if (isset($parentCategory) && $parentCategory)
	<li><a href="{{{ $parentCategory->Route }}}">{{{ $parentCategory->title }}}</a></li>
	@endif
	@if (isset($category) && $category)
	<li><a href="{{{ $category->Route }}}">{{{ $category->title }}}</a></li>
	@endif
	@if (isset($thread) && $thread)
	<li><a href="{{{ $thread->Route }}}">{{{ $thread->title }}}</a></li>
	@endif
</ol>
