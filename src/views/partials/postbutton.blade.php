<?php
$userfunc = \Config::get('forum::integration.currentuser');
$user = $userfunc();
?>
@if ($user != null)
	<a href="{{ $url }}" class="btn btn-primary">{{ $message }}</a>
@endif