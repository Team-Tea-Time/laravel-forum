@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs', ['other' => trans('forum::base.new_threads')])

<h2>{{ trans('forum::base.new_threads') }}</h2>

@if (!$threads->isEmpty())
	<table class="table table-index">
		<thead>
			<tr>
				<th>{{ trans('forum::base.subject') }}</th>
				<th class="col-md-2">{{ trans('forum::base.replies') }}</th>
				<th class="col-md-2 text-right">{{ trans('forum::base.last_post') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($threads as $thread)
				<tr>
					<td>
						<span class="pull-right">
							@if($thread->locked)
								<span class="label label-danger">{{ trans('forum::base.locked') }}</span>
							@endif
							@if($thread->pinned)
								<span class="label label-info">{{ trans('forum::base.pinned') }}</span>
							@endif
							@if($thread->userReadStatus)
								<span class="label label-primary">{{ trans($thread->userReadStatus) }}</span>
							@endif
						</span>
						<p class="lead">
							<a href="{{ $thread->route }}">{{ $thread->title }}</a>
						</p>
						<p>
                            {{ $thread->authorName }}
							<span class="text-muted">(<em><a href="{{ $thread->category->route }}">{{ $thread->category->title }}</a></em>, {{ $thread->posted }})</span>
						</p>
					</td>
					<td>
					    {{ $thread->replyCount }}
					</td>
					<td class="text-right">
					    {{ $thread->lastPost->authorName }}
						<p class="text-muted">({{ $thread->lastPost->posted }})</p>
						<a href="{{ URL::to( $thread->lastPostRoute ) }}" class="btn btn-primary btn-xs">{{ trans('forum::base.view_post') }} &raquo;</a>
					</td>
				</tr>
	        @endforeach
		</tbody>
	</table>

	@if (!is_null($user))
		<div class="text-center">
			<a href="{{ URL::route('forum.post.mark.read') }}"class="btn btn-primary btn-small" data-confirm data-method="post">{{ trans('forum::base.mark_read') }}</a>
		</div>
	@endif
@else
	<p class="text-center">
		{{ trans('forum::base.no_threads') }}
	</p>
@endif
@overwrite
