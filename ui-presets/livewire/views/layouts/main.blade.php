<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>
            @if (isset($thread))
                {{ $thread->title }} —
            @endif
            @if (isset($category))
                {{ $category->title }} —
            @endif
            {{ trans('forum::general.home_title') }}
        </title>

        @vite(['resources/forum/livewire/css/forum.css', 'resources/forum/livewire/js/forum.js'])
    </head>
    <body class="forum bg-slate-200 dark:bg-slate-800">
        <div class="bg-white shadow-md border-b border-slate-100 dark:bg-slate-800 dark:border-slate-700 dark:shadow-none">
            <div class="container flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="/" class="text-lg font-medium">
                    {{ config('app.name') }}
                </a>
                <button data-toggle="navbar" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar" aria-expanded="false">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                    </svg>
                </button>
                <div class="hidden w-full md:block md:w-auto" id="navbar">
                    <ul class="font-medium flex flex-col mt-4 md:mt-0 md:flex-row rtl:space-x-reverse">
                        <li>
                            <a href="{{ route('forum.category.index') }}" class="block hover:bg-slate-100 rounded-md px-4 py-2 md:hover:bg-transparent md:inline">{{ trans('forum::general.home_title') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('forum.recent') }}" class="block hover:bg-slate-100 rounded-md px-4 py-2 md:hover:bg-transparent md:inline">{{ trans('forum::threads.recent') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('forum.unread') }}" class="block hover:bg-slate-100 rounded-md px-4 py-2 md:hover:bg-transparent md:inline">{{ trans('forum::threads.unread_updated') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('forum.category.order') }}" class="block hover:bg-slate-100 rounded-md px-4 py-2 md:hover:bg-transparent md:inline">{{ trans('forum::general.manage') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container mx-auto p-4">
            {{ $slot }}
        </div>

        <livewire:forum::components.alerts />

        <script>
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

            const menuToggle = document.querySelector('[data-toggle]');
            menuToggle.addEventListener('click', () => {
                const id = menuToggle.dataset.toggle;
                const target = document.getElementById(id);
                target.classList.toggle('hidden');
            });
        </script>
    </body>
</html>
