<?php
/*
 * The button should be displayed only when:
 * 		The user is connected (not null)
 * 		if an access model if given, the canPost($user) method return true
 *
 * Otherwise, the button shouldn't be displayed
 */
$userfunc = \Config::get('forum::integration.currentuser');
$user     = $userfunc();
?>
@if (isset($accessModel) && $accessModel->canPost === true)
	<a href="{{ $url }}" class="btn btn-primary">{{{ $message }}}</a>
@endif