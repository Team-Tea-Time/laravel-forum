<div class="alert alert-{{ $type }} alert-dismissable" {!! (!isset($message)) ? 'v-repeat="alert in alerts"' : '' !!}>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<div class="message">
		@if (isset($message))
			{!! $message !!}
		@else
			@{{ alert.message }}
		@endif
	</div>
</div>
