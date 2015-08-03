<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta id="token" name="token" content="{{ csrf_token() }}">

    <title>{!! trans('forum::general.home_title') !!}</title>

    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

    <!-- Vue.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/0.12.9/vue.min.js"></script>
    <!-- Vue.js plugin: vue-resource -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.1.10/vue-resource.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <style>
    body {
        padding: 20px 0;
    }

    textarea {
        min-height: 200px;
    }

    .deleted {
        opacity: 0.35;
    }
    </style>
</head>
<body>
    <script>
    Vue.http.headers.common['X-CSRF-TOKEN'] = $('#token').attr('content');
    </script>

    <div class="container">
        @include ('forum::partials.breadcrumbs')
        @include ('forum::partials.alerts')

        @yield('content')
    </div>
</body>
</html>
