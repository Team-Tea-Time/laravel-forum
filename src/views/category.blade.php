@include('forum::partials.pathdisplay')

@include('forum::partials.postbutton',array('message' => 'New Topic', 'url' => $category->postUrl, 'accessModel' => $category))
@if ($subCategories != NULL && count($subCategories) != 0)
<table class="table table-category">
	<thead>
		<tr>
			<th>Forum</th>
			<th>Topics</th>
			<th>Posts</th>
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
			<td>{{ $subcategory->topicCount }}</td>
			<td>{{ $subcategory->replyCount }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endif

@if ($topics != NULL && count($topics) != 0)
<table class="table table-topic">
	<thead>
		<tr>
			<th>Subject</th>
			<th>Reply</th>
		</tr>
	</thead>
	<tbody>
			@foreach($topics as $topic)
			<tr>
				<th>
					<a href={{$topic->url}}>{{{ $topic->title }}}</a>
				</th>
				<td>{{ $topic->replyCount }}</td>
			</tr>
			@endforeach
	</tbody>
</table>
@endif

@if (($subCategories == NULL || count($subCategories) == 0) && ($topics == NULL || count($topics) == 0))
<table class="table table-topic">
	<thead>
		<tr>
			<th>Subject</th>
			<th>Reply</th>
		</tr>
	</thead>
	<tbody>
			<tr>
				<th>
					No topics found.
				</th>
				<td>
					@include('forum::partials.postbutton',array('message' => 'Post the first!', 'url' => $category->postUrl, 'accessModel' => $category))
				</td>
			</tr>
	</tbody>
</table>
@endif
