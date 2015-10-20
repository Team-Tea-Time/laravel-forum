<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta id="token" name="token" content="{{ csrf_token() }}">

    <title>{!! trans('forum::general.home_title') !!}</title>

    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <style>
    body {
        padding: 30px 0;
    }

    textarea {
        min-height: 200px;
    }

    .deleted {
        opacity: 0.65;
    }
    </style>
</head>
<body>
    <div class="container">
        @include ('forum::partials.breadcrumbs')
        @include ('forum::partials.alerts')

        @yield('content')
    </div>

    <script>
    var toggle = $('input[type=checkbox][data-toggle-all]');
    var checkboxes = $('table tbody input[type=checkbox]');
    var actions = $('[data-actions]');
    var form = $('[data-actions-form]');
    var method = form.find('input[name=_method]');

    function setToggleStates() {
        checkboxes.prop('checked', toggle.is(':checked')).change();
    }

    function setSelectionStates() {
        checkboxes.each(function() {
            var tr = $(this).parents('tr');

            $(this).is(':checked') ? tr.addClass('active') : tr.removeClass('active');

            checkboxes.filter(':checked').length ? actions.removeClass('hidden') : actions.addClass('hidden');
        });
    }

    function setActionStates() {
        var action = actions.find(':selected');

        if (action.attr('data-method')) {
            method.val(action.data('method'));
        } else {
            method.val('patch');
        }

        $('[data-depends]').each(function() {
            (action.val() == $(this).data('depends')) ? $(this).removeClass('hidden') : $(this).addClass('hidden');
        })
    }

    setToggleStates();
    setSelectionStates();
    setActionStates();

    toggle.click(setToggleStates);
    checkboxes.change(setSelectionStates);
    actions.change(setActionStates);

    form.submit(function() {
        var action = actions.find(':selected');

        if (action.attr('data-confirm')) {
            return confirm("{{ trans('forum::general.generic_confirm') }}");
        }

        return true;
    });
    </script>
</body>
</html>
