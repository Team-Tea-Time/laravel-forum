<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>{!! trans('forum::general.home_title') !!}</title>

    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        @include('forum::partials.alerts')

        @yield('content')
    </div>

    <script>
    $('a[data-confirm]').click(function(event) {
        if (!confirm('{!! trans('forum::general.generic_confirm') !!}')) {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
    });
    $('[data-method]:not(.disabled)').click(function(event) {
        $('<form action="' + $(this).attr('href') + '" method="POST">' +
        '<input type="hidden" name="_method" value="' + $(this).data('method') + '">' +
        '<input type="hidden" name="_token" value="{!! csrf_token() !!}"' +
        '</form>').submit();

        event.preventDefault();
    });
    </script>
</body>
</html>
