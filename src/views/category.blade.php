@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

@if($category->canPost)
<p>
	<a href="{{ $category->postAlias }}">{{ trans('forum::base.new_thread') }}</a>
</p>
@endif

@if(!$category->subcategories->isEmpty())
<table class="table table-category">
	<thead>
		<tr>
			<th>{{ trans('forum::base.col_forum') }}</th>
			<th>{{ trans('forum::base.col_threads') }}</th>
			<th>{{ trans('forum::base.col_posts') }}</th>
		</tr>
	</thead>
	<tbody>
		@foreach($category->subcategories as $subcategory)
		<tr>
			<th>
				<div class="category_title">
					<a href={{ $subcategory->URL }}>{{{ $subcategory->title }}}</a>
				</div>
				<div class="category_subtitle">{{{ $subcategory->subtitle }}}</div>
			</th>
			<td>{{ $subcategory->threads->count() }}</td>
			<td>{{ $subcategory->replyCount }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endif

<table class="table table-thread">
	<thead>
		<tr>
			<th>{{ trans('forum::base.subject') }}</th>
			<th>{{ trans('forum::base.reply') }}</th>
			<th>{{ trans('forum::base.last_post') }}</th>
		</tr>
	</thead>
	<tbody>
		@if(!$category->threads->isEmpty())
			@foreach($category->threads as $thread)
			<tr>
				<td>
					<a href={{ $thread->URL }}>{{{ $thread->title }}}</a>
				</td>
				<td>
					{{ $thread->posts->count() }}
				</td>
				<td>
					{{ $thread->lastPost->author->username }}
				</td>
				<td>
					<a href="{{ URL::to( $thread->URL . '?page=' . $thread->lastPage . '#post-' . $thread->lastPost->id ) }}">{{ trans('forum::base.view_post') }} &raquo;</a>
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
					<a href="{{ $category->postAlias }}">{{ trans('forum::base.first_thread') }}</a>
					@endif
				</td>
			</tr>
		@endif
	</tbody>
</table>

@if($category->canPost)
<p>
	<a href="{{ $category->postAlias }}">{{ trans('forum::base.new_thread') }}</a>
</p>
@endif
@overwrite
