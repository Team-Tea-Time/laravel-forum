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

    <!-- Pace.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>

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
        opacity: 0.35;
    }

    /* Vue.js transitions */
    .fade-transition {
        transition: opacity .3s ease;
    }

    .fade-enter, .fade-leave {
        opacity: 0;
    }

    /* Pace.js */
    .pace {
        -webkit-pointer-events: none;
        pointer-events: none;

        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    .pace-inactive {
        display: none;
    }

    .pace .pace-progress {
        background: #000000;
        position: fixed;
        z-index: 2000;
        top: 0;
        right: 100%;
        width: 100%;
        height: 2px;
    }
    </style>
</head>
<body>
    <script>
    Vue.http.headers.common['X-CSRF-TOKEN'] = $('#token').attr('content');

    var confirmMessage = '{{ trans('forum::general.generic_confirm') }}';
    var Forum = Vue.extend({
        data: {
            alerts: []
        },

        methods: {
            addMessage: function (response) {
                this.alerts.push({ message: response.message });
                var self = this;
                setTimeout(function () { self.deleteMessage(self.alerts.length - 1)}, 3000);
            },
            deleteMessage: function (index) {
                this.alerts.splice(index, 1);
            }
        }
    });
    </script>

    <div class="container">
        @include ('forum::partials.breadcrumbs')
        @include ('forum::partials.alerts')

        @yield('content')
    </div>
</body>
</html>
