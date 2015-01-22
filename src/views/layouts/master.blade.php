<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{{ trans('forum::base.home_title') }}</title>
</head>
<body>
	@include('forum::partials.alerts')

	@yield('content')
</body>
</html>
