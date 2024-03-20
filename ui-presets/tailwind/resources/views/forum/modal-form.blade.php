<div tabindex="-1" role="dialog" data-modal="{{ $key }}" class="fixed top-0 left-0 w-full h-full hidden z-10 items-center justify-center">
    <div class="fixed top-0 left-0 w-full h-full bg-black/25" data-close-modal></div>
    <div class="relative bg-white rounded-md max-w-screen-sm w-full m-2" role="document">
        <div class="">
            <div class="border-b px-4 py-3 flex justify-between">
                <h5 class="text-xl font-medium flex items-center gap-1">{!! $title !!}</h5>
                <button type="button" aria-label="Close" data-close-modal>
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

                <div class="p-4">
                    {{ $slot }}
                </div>
                <div class="border-t py-3 px-4 flex justify-end gap-4">
                    <x-forum.button-secondary type="button" class="btn btn-secondary" data-close-modal>{{ trans('forum::general.cancel') }}</x-forum.button-secondary>
                    {{ $actions }}
                </div>
            </form>
        </div>
    </div>
</div>
