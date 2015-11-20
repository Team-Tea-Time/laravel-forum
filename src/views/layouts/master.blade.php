<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <script>window.jQuery || document.write('<script src="//code.jquery.com/jquery-1.11.2.min.js">\x3C/script>')</script>

    <title>{!! trans('forum::base.home_title') !!}</title>
</head>
<body>
    <div class="container">
        @include('forum::partials.alerts')

        @yield('content')
    </div>

    <script>
    $('a[data-confirm]').click(function(event) {
        if (!confirm('{!! trans('forum::base.generic_confirm') !!}')) {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
    });
    $('[data-method]:not(.disabled)').click(function(event) {
        var actionForm = '<form action="' + $(this).attr('href') + '" method="POST">' +
        '<input type="hidden" name="_method" value="' + $(this).data('method') + '">' +
        '<input type="hidden" name="_token" value="{!! Session::getToken() !!}"' +
        '</form>';

        $('[data-action-form]').append(actionForm);
        $('[data-action-form] form').submit();

        event.preventDefault();
    });
    </script>
    <div data-action-form></div>
</body>
</html>
