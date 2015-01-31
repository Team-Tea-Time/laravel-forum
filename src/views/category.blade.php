@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

@if($category->canPost)
<p>
	<a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::base.new_thread') }}</a>
</p>
@endif

@if(!$category->subcategories->isEmpty())
<table class="table table-category">
	<thead>
		<tr>
			<th>{{ trans('forum::base.category') }}</th>
			<th>{{ trans('forum::base.threads') }}</th>
			<th>{{ trans('forum::base.posts') }}</th>
		</tr>
	</thead>
	<tbody>
		@foreach($category->subcategories as $subcategory)
		<tr>
			<th>
				<div class="category_title">
					<a href="{{ $subcategory->Route }}">{{{ $subcategory->title }}}</a>
				</div>
				<div class="category_subtitle">{{{ $subcategory->subtitle }}}</div>
			</th>
			<td>{{ $subcategory->threadCount }}</td>
			<td>{{ $subcategory->replyCount }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endif

{{ $category->pageLinks }}

<table class="table table-thread">
	<thead>
		<tr>
			<th>{{ trans('forum::base.subject') }}</th>
			<th>{{ trans('forum::base.reply') }}</th>
			<th>{{ trans('forum::base.last_post') }}</th>
		</tr>
	</thead>
	<tbody>
		@if(!$category->threadsPaginated->isEmpty())
			@foreach($category->threadsPaginated as $thread)
			<tr>
				<td>
					<a href="{{ $thread->Route }}">
						@if($thread->locked)
						[{{ trans('forum::base.locked') }}]
						@endif
						@if($thread->pinned)
						[{{ trans('forum::base.pinned') }}]
						@endif
						{{{ $thread->title }}}
					</a>
				</td>
				<td>
					{{ $thread->posts->count() }}
				</td>
				<td>
					{{ $thread->lastPost->author->username }}
				</td>
				<td>
					<a href="{{ URL::to( $thread->route . '?page=' . $thread->lastPage . '#post-' . $thread->lastPost->id ) }}">{{ trans('forum::base.view_post') }} &raquo;</a>
				</td>
			</tr>
			@endforeach
		@else
			<tr>
				<td>
					{{ trans('forum::base.no_threads') }}
				</td>
				<td colspan="2">
					@if($category->canPost)
					<a href="{{ $category->newThreadRoute }}">{{ trans('forum::base.first_thread') }}</a>
					@endif
				</td>
			</tr>
		@endif
	</tbody>
</table>

{{ $category->pageLinks }}

@if($category->canPost)
<p>
	<a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::base.new_thread') }}</a>
</p>
@endif
@overwrite
