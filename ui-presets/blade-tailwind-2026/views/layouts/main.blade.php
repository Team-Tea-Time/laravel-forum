<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @if (isset($thread_title))
            {{ $thread_title }} -
        @endif
        @if (isset($category))
            {{ $category->title }} -
        @endif
        {{ trans('forum::general.home_title') }}
    </title>

    @vite(['resources/forum/blade-tailwind/css/forum.css', 'resources/forum/blade-tailwind/js/forum.js'])
</head>
<body class="forum forum-surface-grid">
    <nav class="forum-topbar v-navbar">
        <div class="forum-shell">
            <div class="flex min-h-20 items-center justify-between gap-4">
                <div class="flex items-center gap-8">
                    <a class="forum-brand" href="{{ url(config('forum.frontend.router.prefix')) }}">
                        <span class="forum-brand-mark">F</span>
                        <span>Laravel Forum</span>
                    </a>

                    <div class="hidden items-center gap-1 md:flex">
                        <a class="forum-nav-link" href="{{ url(config('forum.frontend.router.prefix')) }}">{{ trans('forum::general.index') }}</a>
                        <a class="forum-nav-link" href="{{ route('forum.recent') }}">{{ trans('forum::threads.recent') }}</a>
                        @auth
                            <a class="forum-nav-link" href="{{ route('forum.unread') }}">{{ trans('forum::threads.unread_updated') }}</a>
                        @endauth

                        @if (Gate::allows('moveCategories') || Gate::allows('approveThreads') || Gate::allows('approvePosts'))
                            <div class="relative">
                                <button type="button" class="dropdown-toggle forum-nav-link inline-flex items-center gap-2" @click="isManageDropdownCollapsed = !isManageDropdownCollapsed">
                                    {{ trans('forum::general.manage') }}
                                    <i data-feather="chevron-down" class="h-4 w-4"></i>
                                </button>
                                <div class="absolute left-0 top-full mt-3 w-64 overflow-hidden rounded-lg border border-white/75 bg-white/95 p-2 shadow-xl backdrop-blur dark:border-white/10 dark:bg-slate-950/95" :class="{ hidden: isManageDropdownCollapsed }">
                                    @can ('moveCategories')
                                        <a class="block rounded-md px-3 py-2 text-sm font-semibold no-underline hover:bg-slate-100 dark:hover:bg-white/10" href="{{ route('forum.category.manage') }}">
                                            {{ trans('forum::categories.manage') }}
                                        </a>
                                    @endcan
                                    @can ('approveThreads')
                                        <a class="block rounded-md px-3 py-2 text-sm font-semibold no-underline hover:bg-slate-100 dark:hover:bg-white/10" href="{{ route('forum.pending-approval.threads') }}">
                                            {{ trans('forum::threads.pending_approval') }}
                                        </a>
                                    @endcan
                                    @can ('approvePosts')
                                        <a class="block rounded-md px-3 py-2 text-sm font-semibold no-underline hover:bg-slate-100 dark:hover:bg-white/10" href="{{ route('forum.pending-approval.posts') }}">
                                            {{ trans('forum::posts.pending_approval') }}
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="hidden items-center gap-3 md:flex">
                    @if (Auth::check())
                        <div class="relative">
                            <button type="button" class="dropdown-toggle forum-button-secondary" @click="isUserDropdownCollapsed = !isUserDropdownCollapsed">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-cyan-100 text-xs font-black text-cyan-700 dark:bg-cyan-400/15 dark:text-cyan-200">
                                    {{ mb_substr($username, 0, 1) }}
                                </span>
                                {{ $username }}
                                <i data-feather="chevron-down" class="h-4 w-4"></i>
                            </button>
                            <div class="absolute right-0 top-full mt-3 w-52 overflow-hidden rounded-lg border border-white/75 bg-white/95 p-2 shadow-xl backdrop-blur dark:border-white/10 dark:bg-slate-950/95" :class="{ hidden: isUserDropdownCollapsed }">
                                <a class="block rounded-md px-3 py-2 text-sm font-semibold no-underline hover:bg-slate-100 dark:hover:bg-white/10" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Log out
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @else
                        <a class="forum-button-secondary" href="{{ url('/login') }}">Log in</a>
                        <a class="forum-button" href="{{ url('/register') }}">Register</a>
                    @endif
                </div>

                <button class="navbar-toggler forum-button-secondary md:hidden" type="button" :class="{ collapsed: isCollapsed }" @click="isCollapsed = !isCollapsed" aria-label="Toggle navigation">
                    <i data-feather="menu" class="h-5 w-5"></i>
                </button>
            </div>

            <div class="pb-4 md:hidden" :class="{ 'block': !isCollapsed, 'hidden': isCollapsed }">
                <div class="forum-panel-soft p-3">
                    <div class="grid gap-1">
                        <a class="forum-nav-link" href="{{ url(config('forum.frontend.router.prefix')) }}">{{ trans('forum::general.index') }}</a>
                        <a class="forum-nav-link" href="{{ route('forum.recent') }}">{{ trans('forum::threads.recent') }}</a>
                        @auth
                            <a class="forum-nav-link" href="{{ route('forum.unread') }}">{{ trans('forum::threads.unread_updated') }}</a>
                        @endauth
                        @if (Auth::check())
                            <a class="forum-nav-link" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                                Log out
                            </a>
                            <form id="mobile-logout-form" action="{{ url('/logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @else
                            <a class="forum-nav-link" href="{{ url('/login') }}">Log in</a>
                            <a class="forum-nav-link" href="{{ url('/register') }}">Register</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main id="main" class="forum-shell forum-page">
        @include ('forum::partials.breadcrumbs')
        @include ('forum::partials.alerts')

        @yield('content')
    </main>

    @yield('footer')

    <script>
        window.defaultCategoryColor = '{{ config('forum.frontend.default_category_color') }}';
    </script>
</body>
</html>
