<div class="alert alert-{{ $type }} alert-dismissable" @if (!isset($message)) v-show="message" v-transition="fade" @endif>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<div class="message">
		@if (isset($message))
			{!! $message !!}
		@else
			<span v-text="message"></span>
		@endif
	</div>
</div>
