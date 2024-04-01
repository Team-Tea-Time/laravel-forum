<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @if (isset($thread))
                {{ $thread->title }} —
            @endif
            @if (isset($category))
                {{ $category->title }} —
            @endif
            {{ trans('forum::general.home_title') }}
        </title>

        @vite([
            'resources/forum/livewire-tailwind/css/forum.css',
            'resources/forum/livewire-tailwind/js/forum.js'
        ])
    </head>
    <body class="forum bg-slate-200 dark:bg-slate-800">
        <div x-data="navbar">
            <div class="bg-white shadow-md border-b border-slate-100 dark:bg-slate-800 dark:border-slate-700 dark:shadow-none" @click.outside="closeMenu">
                <div class="container mx-auto p-4">
                    <div class="flex flex-col md:flex-row flex-wrap items-center justify-between">
                        <div class="flex w-full md:w-auto items-center justify-between">
                            <div class="grow">
                                <a href="/" class="text-lg font-medium">
                                    {{ config('app.name') }}
                                </a>
                            </div>
                            <button type="button" class="md:hidden inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-slate-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-600" aria-controls="navbar" aria-expanded="false" @click="toggleMenu">
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                                </svg>
                            </button>
                        </div>
                        <div class="w-full md:w-auto md:block font-medium flex flex-col items-center justify-between mt-4 md:mt-0 md:flex-row rtl:space-x-reverse text-center" :class="{ hidden: isMenuCollapsed }">
                            <span class="w-full md:w-auto">
                                <a href="{{ route('forum.category.index') }}" class="block hover:bg-slate-100 dark:hover:bg-slate-600 rounded-md px-4 py-2 md:hover:bg-transparent md:dark:hover:bg-transparent md:inline dark:md:inline">{{ trans('forum::general.home_title') }}</a>
                            </span>
                            <span class="w-full md:w-auto">
                                <a href="{{ route('forum.recent') }}" class="block hover:bg-slate-100 dark:hover:bg-slate-600 rounded-md px-4 py-2 md:hover:bg-transparent md:dark:hover:bg-transparent md:inline">{{ trans('forum::threads.recent') }}</a>
                            </span>
                            @auth
                                <span class="w-full md:w-auto">
                                    <a href="{{ route('forum.unread') }}" class="block hover:bg-slate-100 dark:hover:bg-slate-600 rounded-md px-4 py-2 md:hover:bg-transparent md:dark:hover:bg-transparent md:inline">{{ trans('forum::threads.unread_updated') }}</a>
                                </span>
                            @endauth
                            @can ('moveCategories')
                                <span class="w-full md:w-auto">
                                    <a href="{{ route('forum.category.order') }}" class="block hover:bg-slate-100 dark:hover:bg-slate-600 rounded-md px-4 py-2 md:hover:bg-transparent md:dark:hover:bg-transparent md:inline">{{ trans('forum::general.manage') }}</a>
                                </span>
                            @endcan
                            @if (Auth::check())
                                <span class="relative w-full md:w-auto" @click.outside="closeUserDropdown">
                                    <a class="block flex flex-row items-center place-content-center gap-1 hover:bg-slate-100 dark:hover:bg-slate-600 rounded-md px-4 py-2 md:hover:bg-transparent md:dark:hover:bg-transparent w-full md:w-auto md:inline" href="#" id="navbarDropdownMenuLink" @click="toggleUserDropdown">
                                        {{ $username }}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </a>
                                    <div class="absolute right-0 left-0 md:left-auto w-auto md:w-44 divide-y border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md" :class="{ hidden: isUserDropdownCollapsed }" aria-labelledby="navbarDropdownMenuLink">
                                        <a class="block px-4 py-2" href="#" @click.prevent="logout">
                                            {{ __('Log out') }}
                                        </a>
                                    </div>
                                </span>
                            @else
                                <span class="w-full md:w-auto">
                                    <a href="{{ url('/login') }}" class="block hover:bg-slate-100 dark:hover:bg-slate-600 rounded-md px-4 py-2 md:hover:bg-transparent md:inline">
                                        {{ __('Log in') }}
                                    </a>
                                </span>
                                <span class="w-full md:w-auto">
                                    <a href="{{ url('/register') }}" class="block hover:bg-slate-100 dark:hover:bg-slate-600 rounded-md px-4 py-2 md:hover:bg-transparent md:inline">
                                        {{ __('Register') }}
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto p-4">
            {{ $slot }}
        </div>

        <livewire:forum::components.alerts />

        <script type="module">
            document.addEventListener('alpine:init', () => {
                Alpine.store('time', {
                    now: new Date(),
                    init() {
                        setInterval(() => {
                            this.now = new Date();
                        }, 1000);
                    }
                })
            });

            Alpine.data('navbar', () => {
                return {
                    isMenuCollapsed: true,
                    isUserDropdownCollapsed: true,
                    toggleMenu() {
                        this.isMenuCollapsed = !this.isMenuCollapsed;
                    },
                    closeMenu() {
                        this.isMenuCollapsed = true;
                    },
                    toggleUserDropdown() {
                        this.isUserDropdownCollapsed = !this.isUserDropdownCollapsed;
                    },
                    closeUserDropdown() {
                        this.isUserDropdownCollapsed = true;
                    },
                    logout() {
                        const csrfToken = document.head.querySelector("[name=csrf-token]").content;

                        fetch('/logout', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        }).then(() => { window.location.reload(); });
                    }
                }
            });
        </script>
    </body>
</html>
