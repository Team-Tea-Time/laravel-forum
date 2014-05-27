@foreach ($categories as $category)
<table class="table table-index">
	<thead>
		<tr>
			<td colspan="5">
				<div class="category_title">
					<a href={{$category->url}}>{{ $category->title }}</a>
				</div>
				<div class="category_subtitle">{{ $category->subtitle }}</div>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Forum</th>
			<th>Topics</th>
			<th>Posts</th>
			<th>Last reply</th>
		</tr>
		@foreach($category->subcategories AS $subcategory)
		<tr>
			<th>
				<div class="category_title">
					<a href={{$subcategory->url}}>{{ $subcategory->title }}</a>
				</div>
				<div class="category_subtitle">{{ $subcategory->subtitle }}</div>
			</th>
			<td>{{ $subcategory->topicCount }}</td>
			<td>{{ $subcategory->replyCount }}</td>
			<td>
				@if ($subcategory->lastReply != NULL)
				<a href="{{ $subcategory->lastReply->url }} ">{{ $subcategory->lastReply->topic->title }}</a>
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endforeach
