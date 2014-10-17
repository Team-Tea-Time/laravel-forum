<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<title>{{ trans('forum::base.home_title') }}</title>
	@include('forum::partials.csslinks')

	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	@if(isset($content))
		{{ $content }}
	@else
		{{ trans('forum::base.no_content') }}
	@endif
</body>
</html>