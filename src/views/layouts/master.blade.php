<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{{ trans('forum::base.home_title') }}</title>=
</head>
<body>
	@if(isset($content))
		{{ $content }}
	@else
		{{ trans('forum::base.no_content') }}
	@endif
</body>
</html>
