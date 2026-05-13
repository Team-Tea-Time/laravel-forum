<div tabindex="-1" role="dialog" data-modal="{{ $key }}" class="fixed left-0 top-0 z-50 hidden h-full w-full items-center justify-center p-4">
    <div class="fixed left-0 top-0 h-full w-full bg-slate-950/55 backdrop-blur-sm" data-close-modal></div>
    <div class="forum-panel relative w-full max-w-screen-sm overflow-hidden" role="document">
        <div>
            <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
                <h5 class="flex items-center gap-2 text-lg font-bold text-slate-950 dark:text-white">{!!$title !!}</h5>
                <button type="button" aria-label="Close" data-close-modal class="rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-950 dark:hover:bg-white/10 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ $route }}" method="POST">
                @csrf
                @if (isset($method))
                    @method($method)
                @endif

                <div class="space-y-5 p-6 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    {{ $slot }}
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200/70 px-6 py-5 dark:border-white/10">
                    <x-forum::button-secondary type="button" data-close-modal>{{ trans('forum::general.cancel') }}</x-forum::button-secondary>
                    {{ $actions }}
                </div>
            </form>
        </div>
    </div>
</div>
