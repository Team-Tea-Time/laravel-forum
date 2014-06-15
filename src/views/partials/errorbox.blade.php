@if($errors->has())
	@foreach ($errors->all() as $error)
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			{{ $error }}
		</div>
	@endforeach
@endif