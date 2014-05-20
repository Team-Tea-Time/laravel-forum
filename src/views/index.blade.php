@foreach ($categories as $category)
<table>
	<thead>
		<tr>
			<td colspan="5">
				<div class="category_title">{{ $category->title }}</div>
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
				<div class="category_title">{{ $subcategory->title }}</div>
				<div class="category_subtitle">{{ $subcategory->subtitle }}</div>
			</th>
			<th>{{ $subcategory->topicCount }}</th>
			<th>{{ $subcategory->replyCount }}</th>
			<th>TODO</th>
		</tr>
		@endforeach
	</tbody>
</table>
@endforeach
