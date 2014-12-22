@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

@include('forum::partials.action',array('label' => trans('forum::base.new_thread') , 'url' => $category->postAlias, 'accessModel' => $category))
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
			<td>{{ $subcategory->threadCount }}</td>
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
		</tr>
	</thead>
	<tbody>
			@foreach($threads as $thread)
			<tr>
				<th>
					<a href={{$thread->url}}>{{{ $thread->title }}}</a>
				</th>
				<td>{{ $thread->replyCount }}</td>
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
					@include('forum::partials.action',array('label' => trans('forum::base.first_thread'), 'url' => $category->postAlias, 'accessModel' => $category))
				</td>
			</tr>
	</tbody>
</table>
@endif
@overwrite
