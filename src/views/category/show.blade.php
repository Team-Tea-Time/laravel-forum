@extends('forum::master')

@section('content')
	@include('forum::partials.breadcrumbs')

	<h2>{{ $category->title }}</h2>

	@if (!$category->children->isEmpty())
		<table class="table table-category">
			<thead>
				<tr>
					<th>{{ trans('forum::categories.category') }}</th>
					<th class="col-md-2">{{ trans('forum::threads.threads') }}</th>
					<th class="col-md-2 text-right">{{ trans('forum::posts.posts') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($category->children as $subcategory)
					@include('forum::category.partials.list')
				@endforeach
			</tbody>
		</table>
	@endif

	<div class="row">
		<div class="col-xs-4">
			@if ($category->userCanCreateThreads)
				<a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
			@endif
		</div>
		<div class="col-xs-8 text-right">
			{!! $category->pageLinks !!}
		</div>
	</div>

	<table class="table table-thread">
		<thead>
			<tr>
				<th>{{ trans('forum::general.subject') }}</th>
				<th class="col-md-2 text-right">{{ trans('forum::general.replies') }}</th>
				<th class="col-md-2 text-right">{{ trans('forum::posts.last') }}</th>
			</tr>
		</thead>
		<tbody>
			@if (!$category->threadsPaginated->isEmpty())
				@foreach ($category->threadsPaginated as $thread)
					<tr>
						<td>
							<span class="pull-right">
								@if($thread->locked)
									<span class="label label-danger">{{ trans('forum::threads.locked') }}</span>
								@endif
								@if($thread->pinned)
									<span class="label label-info">{{ trans('forum::threads.pinned') }}</span>
								@endif
								@if($thread->userReadStatus)
									<span class="label label-primary">{{ trans($thread->userReadStatus) }}</span>
								@endif
							</span>
							<p class="lead">
								<a href="{{ $thread->route }}">{{ $thread->title }}</a>
							</p>
							<p>{{ $thread->authorName }} <span class="text-muted">({{ $thread->posted }})</span></p>
						</td>
						<td class="text-right">
						    {{ $thread->replyCount }}
						</td>
						<td class="text-right">
						    {{ $thread->lastPost->authorName }}
							<p class="text-muted">({{ $thread->lastPost->posted }})</p>
							<a href="{{ URL::to( $thread->lastPostRoute ) }}" class="btn btn-primary btn-xs">{{ trans('forum::posts.view') }} &raquo;</a>
						</td>
					</tr>
				@endforeach
			@else
				<tr>
					<td>
						{{ trans('forum::threads.none_found') }}
					</td>
					<td colspan="2">
						@if ($category->userCanCreateThreads)
							<a href="{{ $category->newThreadRoute }}">{{ trans('forum::threads.post_the_first') }}</a>
						@endif
					</td>
				</tr>
			@endif
		</tbody>
	</table>

	<div class="row">
		<div class="col-xs-4">
			@if ($category->userCanCreateThreads)
				<a href="{{ $category->newThreadRoute }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
			@endif
		</div>
		<div class="col-xs-8 text-right">
			{!! $category->pageLinks !!}
		</div>
	</div>
@overwrite
