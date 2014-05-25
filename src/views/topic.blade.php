<link href="{{ asset('packages/atrakeur/forum/css/forum-design.css') }}" rel="stylesheet" type="text/css" />

<div class="category_header">
	@if ($parentCategory)
		<a href="{{ $category->parentCategory->url }}">{{$category->parentCategory->title}}</a>
	@endif
	@if ($category)
		<a href="{{ $category->url }}">{{$category->title}}</a>
	@endif
	@if ($topic)
		<a href="{{ $topic->url }}">{{$topic->title}}</a>
	@endif
</div>

<table class="table table-index">
	<thead>
		<tr>
			<td>
				Auteur
			</td>
			<td>
				Message
			</td>
		</tr>
	</thead>
	<tbody>
		@foreach ($messages as $message)
		<tr>
			<td>
				Un auteur
			</td>
			<td>
				{{ $message->data }}
			</td>
		</tr>
		<tr>
			<td>
				
			</td>
			<td>
				{{ $message->created_at }}
			</td>
		</tr>
		@endforeach
	</tbody>	
</table>
{{ $messages->links() }}
