@if (isset($accessModel) && $accessModel->canPost)
	<a href="{{ $url }}" class="btn btn-primary">{{{ $label }}}</a>
@endif
