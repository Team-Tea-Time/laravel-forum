<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @if (isset($thread))
            {{ $thread->title }} -
        @endif
        @if (isset($category))
            {{ $category->title }} -
        @endif
        {{ trans('forum::general.home_title') }}
    </title>

    <!-- Bootstrap (https://github.com/twbs/bootstrapS) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Vue (https://github.com/vuejs/vue) -->
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>

    <!-- Axios (https://github.com/axios/axios) -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <!-- Pickr (https://github.com/Simonwep/pickr) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>

    <!-- Sortable (https://github.com/SortableJS/Sortable) -->
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.10.1/Sortable.min.js"></script>
    <!-- Vue.Draggable (https://github.com/SortableJS/Vue.Draggable) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.23.2/vuedraggable.umd.min.js"></script>

    <style>
    body {
        padding: 0;
        background: #f8fafc;
    }

    textarea {
        min-height: 200px;
    }

    table tr td {
        white-space: nowrap;
    }

    .deleted {
        opacity: 0.65;
    }

    #main {
        padding: 2em;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card.category {
        margin-bottom: 1em;
    }

    .modal {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    .list-group .list-group {
        min-height: 1em;
        margin-top: .5em;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .3s ease;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }

    .slide-fade-enter-active, .slide-fade-leave-active {
        transition: .3s ease;
    }
    .slide-fade-enter {
        opacity: 0;
        transform: translateY(-5%);
    }
    .slide-fade-leave-to {
        opacity: 0;
        transform: translateY(-5%);
    }
    </style>
</head>
<body>
    <nav class="v-navbar navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix')) }}">Laravel Forum</a>
            <button class="navbar-toggler" type="button" @click="isExpanded = !isExpanded">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" :class="{ show: isExpanded }">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url(config('forum.frontend.router.prefix')) }}">{{ trans('forum::general.index') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('forum.new.index') }}">{{ trans('forum::threads.new_updated') }}</a>
                    </li>
                    @can ('moveCategories')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forum.category.manage') }}">{{ trans('forum::general.manage') }}</a>
                        </li>
                    @endcan
                </ul>
                <ul class="navbar-nav">
                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" @click="isUserDropdownOpen = !isUserDropdownOpen">
                                {{ $username }}
                            </a>
                            <div class="dropdown-menu" :class="{ show: isUserDropdownOpen }" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Log out
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/login') }}">Log in</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/register') }}">Register</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div id="main" class="container">
        @include ('forum::partials.breadcrumbs')
        @include ('forum::partials.alerts')

        @yield('content')
    </div>

    <script>
    new Vue({
        el: '.v-navbar',
        data: {
            isExpanded: false,
            isUserDropdownOpen: false
        },
        methods: {
            onWindowClick (event) {
                if (event.target.classList.contains('dropdown-toggle')) return;
                if (this.isExpanded) this.isExpanded = false;
                if (this.isUserDropdownOpen) this.isUserDropdownOpen = false;
            }
        },
        created: function () {
            window.addEventListener('click', this.onWindowClick);
        }
    });
    </script>
    @yield('footer')
</body>
</html>
