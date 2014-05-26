<link href="{{ asset('packages/atrakeur/forum/css/forum-design.css') }}" rel="stylesheet" type="text/css" />

<div class="category_header">
	@if ($parentCategory)
		<a href="{{ $category->parentCategory->url }}">{{$category->parentCategory->title}}</a>
	@endif
	@if ($category)
		<a href="{{ $category->url }}">{{$category->title}}</a>
	@endif
</div>

@if ($subCategories != NULL && $subCategories->count() != 0)
<table class="table table-category">
	<thead>
		<tr>
			<th>Forum</th>
			<th>Topics</th>
			<th>Posts</th>
			<th>Last reply</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($subCategories as $subcategory)
		<tr>
			<th>
				<div class="category_title">
					<a href={{$subcategory->url}}>{{ $subcategory->title }}</a>
				</div>
				<div class="category_subtitle">{{ $subcategory->subtitle }}</div>
			</th>
			<td>{{ $subcategory->topicCount }}</td>
			<td>{{ $subcategory->replyCount }}</td>
			<td>TODO</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endif

<table class="table table-topic">
	<thead>
		<tr>
			<th>Subject</th>
			<th>Reply</th>
			<th>Last reply</th>
		</tr>
	</thead>
	<tbody>
		@if ($topics != NULL && $topics->count() != 0)
			@foreach($topics as $topic)
			<tr>
				<th>
					<a href={{$topic->url}}>{{ $topic->title }}</a>
				</th>
				<td>TODO</td>
				<td>TODO</td>
			</tr>
			@endforeach
		@else
			<tr>
				<td colspan="3">No topics found</td>
			</tr>
		@endif
	</tbody>
</table>

