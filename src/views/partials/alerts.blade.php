@if (Session::has('alerts'))
	@foreach (Session::get('alerts') as $alert)
		<div class="alert alert-{{ $alert['type'] }} alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			{!! $alert['message'] !!}
		</div>
	@endforeach
@endif
