@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

@if( $category->canPost )
<p>
	<a href="{{ $category->postAlias }}">{{ trans('forum::base.new_thread') }}</a>
</p>
@endif

@if ($subCategories != NULL && count($subCategories) != 0)
<table class="table table-category">
	<thead>
		<tr>
			<th>{{ trans('forum::base.col_forum') }}</th>
			<th>{{ trans('forum::base.col_threads') }}</th>
			<th>{{ trans('forum::base.col_posts') }}</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($subCategories as $subcategory)
		<tr>
			<th>
				<div class="category_title">
					<a href={{$subcategory->url}}>{{{ $subcategory->title }}}</a>
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

@if ($threads != NULL && count($threads) != 0)
<table class="table table-thread">
	<thead>
		<tr>
			<th>{{ trans('forum::base.subject') }}</th>
			<th>{{ trans('forum::base.reply') }}</th>
			<th>{{ trans('forum::base.last_post') }}</th>
		</tr>
	</thead>
	<tbody>
			@foreach($threads as $thread)
			<tr>
				<th>
					<a href={{ $thread->URL }}>{{{ $thread->title }}}</a>
				</th>
				<td>{{ $thread->posts->count() }}</td>
				<td>
					<a href="{{ URL::to( $thread->posts->sortBy('created_at')->first()->author->profile->route ) }}">{{ $thread->posts->sortBy('created_at')->first()->author->username }}</a>
				</td>
				<td>
					<a href="{{ URL::to( $thread->URL . '?page=' . $thread->lastPage . '#post-' . $thread->posts->sortBy('created_at')->first()->id ) }}">{{ trans('forum::base.view_post') }} &raquo;</a>
				</td>
			</tr>
			@endforeach
	</tbody>
</table>
@endif

@if (($subCategories == NULL || count($subCategories) == 0) && ($threads == NULL || count($threads) == 0))
<table class="table table-thread">
	<thead>
		<tr>
			<th>{{ trans('forum::base.subject') }}</th>
			<th>{{ trans('forum::base.reply') }}</th>
		</tr>
	</thead>
	<tbody>
			<tr>
				<th>
					{{ trans('forum::base.no_threads') }}
				</th>
				<td>
					@if( $category->canPost )
					<a href="{{ $category->postAlias }}">{{ trans('forum::base.first_thread') }}</a>
					@endif
				</td>
			</tr>
	</tbody>
</table>
@endif
@overwrite

@if( $category->canPost )
<p>
	<a href="{{ $category->postAlias }}">{{ trans('forum::base.new_thread') }}</a>
</p>
@endif
