@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

@foreach ($categories as $category)
<table class="table table-index">
	<thead>
		<tr>
			<td colspan="3">
				<a href="{{ $category->Route }}">{{{ $category->title }}}</a>
				{{{ $category->subtitle }}}</div>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>{{ trans('forum::base.category') }}</th>
			<th>{{ trans('forum::base.threads') }}</th>
			<th>{{ trans('forum::base.posts') }}</th>
		</tr>
		@if (!$category->subcategories->isEmpty())
		@foreach ($category->subcategories as $subcategory)
		<tr>
			<td>
				<a href="{{ $subcategory->Route }}">{{{ $subcategory->title }}}</a>
				<br>
				{{{ $subcategory->subtitle }}}
				@if ($subcategory->newestThread)
				<br>
				{{ trans('forum::base.newest_thread') }}: <a href="{{ $subcategory->newestThread->route }}">{{{ $subcategory->newestThread->title }}}</a>
				({{{ $subcategory->newestThread->author->username }}})
				<br>
				{{ trans('forum::base.last_post') }}: <a href="{{ $subcategory->latestActiveThread->lastPost->route }}">{{{ $subcategory->latestActiveThread->lastPost->title }}}</a>
				({{{ $subcategory->latestActiveThread->lastPost->username }}})
				@endif
			</td>
			<td>{{ $subcategory->threadCount }}</td>
			<td>{{ $subcategory->replyCount }}</td>
		</tr>
		@endforeach
		@else
		<tr>
			<th colspan="3">
				{{ trans('forum::base.no_categories') }}
			</th>
		</tr>
		@endif
	</tbody>
</table>
@endforeach
@overwrite
