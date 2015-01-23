<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<script>window.jQuery || document.write('<script src="//code.jquery.com/jquery-1.11.2.min.js">\x3C/script>')</script>

	<title>{{ trans('forum::base.home_title') }}</title>
</head>
<body>
	@include('forum::partials.alerts')

	@yield('content')

	<script>
	$('form a[data-submit]').click(function() {
		$(this).parent('form').submit();
	});

	$('form[data-confirm]').submit(function() {
		return confirm('{{ trans('forum::base.generic_confirm') }}');
	});
	</script>
</body>
</html>
