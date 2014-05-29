<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Laravel forum</title>
	@include('forum::partials.csslinks')

	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	@if(isset($content))
		{{ $content }}
	@else
		Nothing to display here (did you set $this->layout->content in your controller?)
	@endif
</body>
</html>