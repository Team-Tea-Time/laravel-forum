@if ($paginator->hasPages())
    <nav class="my-4 flex justify-center">
        <ul class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white/70 p-1 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/[0.055]">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link grid h-9 min-w-9 place-items-center rounded-full px-3 text-slate-300 dark:text-slate-700" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link grid h-9 min-w-9 place-items-center rounded-full px-3 text-sm font-bold no-underline hover:bg-slate-100 dark:hover:bg-white/10" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link grid h-9 min-w-9 place-items-center rounded-full px-3 text-sm text-slate-400">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link grid h-9 min-w-9 place-items-center rounded-full bg-slate-950 px-3 text-sm font-bold text-white dark:bg-white dark:text-slate-950">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link grid h-9 min-w-9 place-items-center rounded-full px-3 text-sm font-bold no-underline hover:bg-slate-100 dark:hover:bg-white/10" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link grid h-9 min-w-9 place-items-center rounded-full px-3 text-sm font-bold no-underline hover:bg-slate-100 dark:hover:bg-white/10" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link grid h-9 min-w-9 place-items-center rounded-full px-3 text-slate-300 dark:text-slate-700" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
