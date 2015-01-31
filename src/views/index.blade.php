@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

@foreach ($categories as $category)
<table class="table table-index">
	<thead>
		<tr>
			<td colspan="3">
				<div class="category_title">
					<a href="{{ $category->Route }}">{{{ $category->title }}}</a>
				</div>
				<div class="category_subtitle">{{{ $category->subtitle }}}</div>
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
